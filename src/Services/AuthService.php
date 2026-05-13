<?php
declare(strict_types=1);
namespace App\Services;

use App\Repositories\UsuarioRepository;
use Exception;

class AuthService
{
    private UsuarioRepository $repo;

    public function __construct(?UsuarioRepository $repo = null)
    {
        // Usa o repositório informado ou cria o padrão para buscar usuários.
        $this->repo = $repo ?? new UsuarioRepository();

        // Garante sessão ativa para login, logout e identificação do usuário.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Valida credenciais e salva os dados do usuário na sessão.
    public function login(string $email, string $senha): void
    {
        $maxTentativas = 5;
        $tempoBloqueio = 300; // 5 minutos em segundos

        // 1. Verifica se a conta/IP já está no período de bloqueio
        if (isset($_SESSION['login_tentativas']) && $_SESSION['login_tentativas'] >= $maxTentativas) {
            $tempoPassado = time() - $_SESSION['ultimo_erro_login'];
            if ($tempoPassado < $tempoBloqueio) {
                $tempoRestante = ceil(($tempoBloqueio - $tempoPassado) / 60);
                throw new Exception("Muitas tentativas falhas. Tente novamente em {$tempoRestante} minutos.");
            } else {
                // O tempo de punição acabou, zera as tentativas
                $_SESSION['login_tentativas'] = 0;
            }
        }

        // 2. Busca o usuário utilizando a propriedade correta ($this->repo)
        $usuario = $this->repo->findByEmail($email);

        // 3. Verifica se o usuário existe e se a senha confere
        if (!$usuario || !password_verify($senha, $usuario->senha)) {
            // Conta mais um erro no painel de tentativas
            $_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;
            $_SESSION['ultimo_erro_login'] = time();

            throw new Exception("E-mail ou senha inválidos.");
        }

        // NOVO: Verifica se o hash antigo precisa ser atualizado para o Argon2id
        $options = [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 2
        ];

        if (password_needs_rehash($usuario->senha, PASSWORD_ARGON2ID, $options)) {
            $novoHash = password_hash($senha, PASSWORD_ARGON2ID, $options);
            $this->repo->updateSenha($usuario->id, $novoHash);
        }

        // 4. Sucesso! Zera as falhas e guarda os dados na sessão
        $_SESSION['login_tentativas'] = 0;
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
    }


    // Limpa a sessão e encerra a autenticação.
    public function logout(): void
    {
        unset($_SESSION['usuario_id'], $_SESSION['usuario_nome']);
        session_destroy();
    }

    // Retorna o ID do usuário logado, se existir.
    public function getLoggedUserId(): ?int
    {
        return $_SESSION['usuario_id'] ?? null;
    }

    // Confere se existe usuário autenticado na sessão.
    public function check(): bool
    {
        return isset($_SESSION['usuario_id']);
    }
}