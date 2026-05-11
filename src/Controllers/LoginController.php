<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Services\AuthService;

class LoginController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    /**
     * Exibe a tela de login
     */
    public function index(): void
    {
        // Se já estiver logado, redireciona para o painel de viações
        if ($this->auth->check()) {
            header('Location: /admin/viacoes');
            exit;
        }

        $erro = $_SESSION['login_erro'] ?? null;
        unset($_SESSION['login_erro']);

        require __DIR__ . '/../views/login.php';
    }

    /**
     * Processa a tentativa de login (POST /login)
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // Log a tentativa de ataque aqui (prática de SOC)
                die('Erro de segurança: Token CSRF inválido ou expirado.');
            }
        }
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        try {
            // O AuthService utiliza password_verify para validar o hash do banco
            $this->auth->login($email, $senha);

            header('Location: /admin/viacoes');
            exit;
        } catch (\Exception $e) {
            // Login falhou (senha errada ou e-mail inexistente)
            $_SESSION['login_erro'] = $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    /**
     * Faz o logout e volta para a home
     */
    public function sair(): void
    {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}