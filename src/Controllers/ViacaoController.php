<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use App\Services\AuthService;
use App\DTOs\ViacaoDTO;
use Exception;

final class ViacaoController
{
    private ViacaoService $service;
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }
        $this->service = new ViacaoService();
    }

    // Método centralizado para verificação de segurança (SOC/Blue Team)
    private function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Utilizamos o operador de coalescência na sessão para evitar warnings caso não exista
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
                // TODO: Adicionar Monolog aqui para registrar a tentativa de ataque no SIEM
                die('Erro de segurança: Token CSRF inválido ou expirado.');
            }
        }
    }

    public function index(): void
    {
        $busca  = (string)($_GET['nome'] ?? '');
        $status = (string)($_GET['status'] ?? '');
        $ordem  = (string)($_GET['order'] ?? 'criado_em');
        $dir    = (string)($_GET['dir'] ?? 'DESC');

        View::render('admin/viacoes/index', [
            'viacoes' => $this->service->all($busca, $status, $ordem, $dir),
            'filtros' => compact('busca', 'status', 'ordem', 'dir')
        ]);
    }

    public function create(): void
    {
        View::render('admin/viacoes/create', [
            'errors' => [],
            'old' => ['nome' => '', 'url' => '', 'cidade' => '', 'status' => 'ativo']
        ]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        try {
            $dto = ViacaoDTO::fromRequest($_POST, $_FILES['logo'] ?? null);
            $this->service->create($dto);

            header('Location: /admin/viacoes');
            exit;
        } catch (Exception $e) {
            View::render('admin/viacoes/create', [
                'errors' => explode('|', $e->getMessage()),
                'old' => $_POST
            ]);
        }
    }

    public function edit(int $id): void
    {
        $viacao = $this->service->find($id);
        if (!$viacao) {
            header('Location: /admin/viacoes');
            exit;
        }

        View::render('admin/viacoes/edit', [
            'viacao' => $viacao,
            'errors' => [],
            'old' => (array) $viacao
        ]);
    }

    public function update(int $id): void
    {
        $this->verifyCsrf();

        try {
            $dto = ViacaoDTO::fromRequest($_POST, $_FILES['logo'] ?? null);
            $this->service->update($id, $dto);

            header('Location: /admin/viacoes');
            exit;
        } catch (Exception $e) {
            $viacao = $this->service->find($id);
            View::render('admin/viacoes/edit', [
                'viacao' => $viacao,
                'errors' => explode('|', $e->getMessage()),
                'old' => $_POST
            ]);
        }
    }

    public function destroy(int $id): void
    {
        // Poderíamos chamar o verifyCsrf() aqui também caso a requisição delete seja via POST method spoofing
        $this->verifyCsrf();

        $this->service->delete($id);
        header('Location: /admin/viacoes');
        exit;
    }
}