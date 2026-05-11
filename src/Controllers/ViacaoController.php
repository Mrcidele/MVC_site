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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Acesso negado: Falha na validação de segurança (CSRF Token).');
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // Log a tentativa de ataque aqui (prática de SOC)
                die('Erro de segurança: Token CSRF inválido ou expirado.');
            }
        }
        try {
            // Conversão imediata para DTO
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('Acesso negado: Falha na validação de segurança (CSRF Token).');
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                // Log a tentativa de ataque aqui (prática de SOC)
                die('Erro de segurança: Token CSRF inválido ou expirado.');
            }
        }
        try {
            // Conversão imediata para DTO
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
        $this->service->delete($id);
        header('Location: /admin/viacoes');
        exit;
    }
}