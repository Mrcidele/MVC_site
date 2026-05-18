<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;

class LogAcessoRepository
{
    private PDO $db;

    // Permite que o construtor seja chamado sem argumentos (?PDO $db = null)
    public function __construct(?PDO $db = null)
    {
        // Se a conexão não for passada, ele puxa a conexão singleton global do db.php
        $this->db = $db ?? \getPdo();
    }
    public function registrar(string $email, ?int $usuarioId, string $status, string $detalhes): void
    {
        $ip = $this->obterIpReal();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';

        $stmt = $this->db->prepare("
            INSERT INTO logs_acesso (email_tentativa, usuario_id, ip_origem, user_agent, status, detalhes)
            VALUES (:email, :usuario_id, :ip, :user_agent, :status, :detalhes)
        ");

        $stmt->execute([
            ':email' => $email,
            ':usuario_id' => $usuarioId,
            ':ip' => $ip,
            ':user_agent' => $userAgent,
            ':status' => $status,
            ':detalhes' => $detalhes
        ]);
    }

    public function listarTodos(): array
    {
        $stmt = $this->db->query("SELECT * FROM logs_acesso ORDER BY data_hora DESC LIMIT 500");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Extrair o verdadeiro IP do cliente,
     * ignorando proxies, load balancers e gateways do Docker.
     */
    private function obterIpReal(): string
    {
        // Lista de cabeçalhos em ordem de prioridade.
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // 1. Cloudflare (Muito comum em produção)
            'HTTP_X_REAL_IP',            // 2. Nginx Reverse Proxy
            'HTTP_X_FORWARDED_FOR',      // 3. Padrão da indústria para Proxies/Load Balancers
            'HTTP_CLIENT_IP',            // 4. Cabeçalho de cliente genérico
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'                // Último recurso
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                // O primeiro IP da lista é SEMPRE o dispositivo original do utilizador.
                $listaIps = explode(',', $_SERVER[$header]);

                foreach ($listaIps as $ip) {
                    $ip = trim($ip);

                    // Valida se o que recebemos é realmente um IP válido (IPv4 ou IPv6)
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {

                        // Se o IP for a rede interna do Docker (172.x.x.x) E ainda tivermos
                        // outros cabeçalhos para verificar, ignoramos e tentamos achar o IP público.
                        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip) && $header !== 'REMOTE_ADDR') {
                            continue;
                        }

                        return $ip;
                    }
                }
            }
        }

        // Fallback final. No teu ambiente local do Docker, vai cair aqui e devolver o 172.x.x.x
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}