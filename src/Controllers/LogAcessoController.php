<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Repositories\LogAcessoRepository;
use App\Services\AuthService;
use App\Core\View;

class LogAcessoController
{
    private LogAcessoRepository $logRepo;
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();

        // Bloqueio de acesso: Apenas admins autenticados podem ver os logs
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }

        $this->logRepo = new LogAcessoRepository();
    }

    public function index(): void
    {
        $logs = $this->logRepo->listarTodos();

        View::render('admin/logs/index', [
            'logs' => $logs
        ]);
    }
}