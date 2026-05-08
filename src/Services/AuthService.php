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
        $usuario = $this->repo->findByEmail($email);

        // Se o usuário não existir ou a senha estiver errada, bloqueia o acesso.
        if (!$usuario || !password_verify($senha, $usuario->senha)) {
            throw new Exception("E-mail ou senha inválidos.");
        }

        // Guarda os dados básicos na sessão para manter o login ativo.
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