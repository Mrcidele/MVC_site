<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\LogAcessoRepository;

class LoginController
{
    private AuthService $auth;
    private LogAcessoRepository $logRepo;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->logRepo = new LogAcessoRepository();
    }

    private function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {

                // SOC: Regista tentativas de forjar a requisição (CSRF)
                $emailTentado = $_POST['email'] ?? 'Desconhecido';
                $this->logRepo->registrar(
                    $emailTentado,
                    null,
                    'falha',
                    'Ataque HTTP: Token CSRF inválido, ausente ou expirado.'
                );

                die('Erro de segurança: Token CSRF inválido ou expirado.');
            }
        }
    }

    public function index(): void
    {
        if ($this->auth->check()) {
            header('Location: /admin/viacoes');
            exit;
        }

        $erro = $_SESSION['login_erro'] ?? null;
        unset($_SESSION['login_erro']);

        require __DIR__ . '/../views/login.php';
    }

    public function authenticate(): void
    {
        $this->verifyCsrf();

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        try {
            $this->auth->login($email, $senha);
            header('Location: /admin/viacoes');
            exit;
        } catch (\Exception $e) {
            $_SESSION['login_erro'] = $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    public function sair(): void
    {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}