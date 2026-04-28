<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Models\Historico;
use PDO;


// Repositório de Auditoria: Gerencia o log de eventos do sistema.
final class HistoricoRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    // Recupera todos os registros de auditoria em ordem cronológica inversa.
    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM viacoes_historico ORDER BY data_hora DESC");
        return array_map(fn($row) => new Historico(
            (int) $row['id'],
            $row['viacao_id'] ? (int) $row['viacao_id'] : null,
            $row['acao'],
            $row['detalhes'],
            $row['data_hora']
        ), $stmt->fetchAll());
    }

    // Registra um novo evento de auditoria no banco de dados.
    public function log(?int $viacaoId, string $acao, string $detalhes): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO viacoes_historico (viacao_id, acao, detalhes) VALUES (?, ?, ?)");
        $stmt->execute([$viacaoId, $acao, $detalhes]);
    }
}