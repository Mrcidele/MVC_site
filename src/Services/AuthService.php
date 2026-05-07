<?php
declare(strict_types=1);
namespace App\Services;

use App\Repositories\UsuarioRepository;
use Exception;

final class AuthService
{
    private UsuarioRepository $repo;

    public function __construct(?UsuarioRepository $repo = null)
    {
        $this->repo = $repo ?? new UsuarioRepository();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(string $email, string $senha): void
    {
        $usuario = $this->repo->findByEmail($email);

        if (!$usuario || !password_verify($senha, $usuario->senha)) {
            throw new Exception("E-mail ou senha inválidos.");
        }

        $_SESSION['usuario_id'] = $usuario->id;
        $_SESSION['usuario_nome'] = $usuario->nome;
    }

    public function logout(): void
    {
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