<?php
declare(strict_types=1);
namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Repositories\LogAcessoRepository;
use Exception;

class AuthService
{
    private UsuarioRepository $repo;
    private LogAcessoRepository $logRepo;

    public function __construct(?UsuarioRepository $repo = null, ?LogAcessoRepository $logRepo = null)
    {
        $this->repo = $repo ?? new UsuarioRepository();
        $this->logRepo = $logRepo ?? new LogAcessoRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $email, string $senha): void
    {
        $maxTentativas = 5;
        $tempoBloqueio = 300;

        // 1. Rate Limiting
        if (isset($_SESSION['login_tentativas']) && $_SESSION['login_tentativas'] >= $maxTentativas) {
            $tempoPassado = time() - $_SESSION['ultimo_erro_login'];
            if ($tempoPassado < $tempoBloqueio) {
                $tempoRestante = ceil(($tempoBloqueio - $tempoPassado) / 60);

                // SOC: Log da tentativa enquanto bloqueado
                $this->logRepo->registrar($email, null, 'falha', 'Ataque mitigado por Rate Limit');
                throw new Exception("Muitas tentativas falhas. Tente novamente em {$tempoRestante} minutos.");
            } else {
                $_SESSION['login_tentativas'] = 0;
            }
        }

        // 2. Busca o usuário
        $usuario = $this->repo->findByEmail($email);

        // 3. Validação
        if (!$usuario || !password_verify($senha, $usuario->senha)) {
            $_SESSION['login_tentativas'] = ($_SESSION['login_tentativas'] ?? 0) + 1;
            $_SESSION['ultimo_erro_login'] = time();

            // SOC: Log detalhado do erro real (apenas no banco)
            $motivo = $usuario ? 'Senha incorreta' : 'E-mail inexistente no sistema';
            $this->logRepo->registrar($email, null, 'falha', $motivo);

            throw new Exception("E-mail ou senha inválidos.");
        }

        // 4. Migração dinâmica de Hash
        $options = ['memory_cost' => 65536, 'time_cost' => 4, 'threads' => 2];
        if (password_needs_rehash($usuario->senha, PASSWORD_ARGON2ID, $options)) {
            $novoHash = password_hash($senha, PASSWORD_ARGON2ID, $options);
            $this->repo->updateSenha($usuario->id, $novoHash);
        }

        // 5. Sucesso
        $_SESSION['login_tentativas'] = 0;
        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;

        // SOC: Log de sucesso
        $this->logRepo->registrar($email, $usuario->id, 'sucesso', 'Autenticação bem-sucedida');
    }

    public function logout(): void
    {
        if ($this->check()) {
            $this->logRepo->registrar('Sessão encerrada', $this->getLoggedUserId(), 'logout', 'Logout manual efetuado');
        }

        unset($_SESSION['usuario_id'], $_SESSION['usuario_nome']);
        session_destroy();
    }

    public function getLoggedUserId(): ?int
    {
        return $_SESSION['usuario_id'] ?? null;
    }

    public function check(): bool
    {
        return isset($_SESSION['usuario_id']);
    }
}