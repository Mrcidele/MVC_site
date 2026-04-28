<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use Exception;

//Controller Administrativo para Gestão de Viações
final class ViacaoController
{
    private ViacaoService $viacoes;

    public function __construct(?ViacaoService $viacoes = null)
    {
        $this->viacoes = $viacoes ?? new ViacaoService();
    }

    //Listagem com filtros e ordenação
    public function index(): void
    {
        $busca = trim((string) ($_GET['nome'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));
        $ordem = trim((string) ($_GET['order'] ?? 'nome'));
        $dir = trim((string) ($_GET['dir'] ?? 'ASC'));

        View::render('admin/viacoes/index', [
            'viacoes' => $this->viacoes->all($busca, $status, $ordem, $dir),
            'filtros' => compact('busca', 'status', 'ordem', 'dir')
        ]);
    }

    //Formulário de criação
    public function create(): void
    {
        View::render('admin/viacoes/create', [
            'errors' => [],
            'old' => ['nome' => '', 'url' => '', 'cidade' => '', 'status' => 'ativo']
        ]);
    }

    //Processa a criação (POST)
    public function store(): void
    {
        try {
            $this->viacoes->create($_POST, $this->getUploadedLogo());

            View::flash('success', "Viação cadastrada com sucesso!");
            View::redirect('/admin/viacoes');
        } catch (Exception $e) {
            View::render('admin/viacoes/create', [
                'errors' => explode('|', $e->getMessage()),
                'old' => $_POST
            ]);
            return;
        }
    }

    //Formulário de edição
    public function edit(int $id): void
    {
        $viacao = $this->viacoes->find($id);

        if ($viacao === null) {
            http_response_code(404);
            echo "Viação não encontrada.";
            exit;
        }

        View::render('admin/viacoes/edit', [
            'viacao' => $viacao,
            'errors' => [],
            'old' => ['nome' => $viacao->nome, 'url' => $viacao->url, 'cidade' => $viacao->cidade, 'status' => $viacao->status]
        ]);
    }

    //Processa a atualização (PUT)
    public function update(int $id): void
    {
        try {
            $this->viacoes->update($id, $_POST, $this->getUploadedLogo());

            View::flash('success', "Viação atualizada com sucesso!");
            View::redirect('/admin/viacoes');
        } catch (Exception $e) {
            $viacao = $this->viacoes->find($id);

            View::render('admin/viacoes/edit', [
                'viacao' => $viacao,
                'errors' => explode('|', $e->getMessage()),
                'old' => $_POST
            ]);
            return;
        }
    }

    //Remove uma viação (DELETE)
    public function destroy(int $id): void
    {
        $this->viacoes->delete($id);
        View::flash('success', "Viação removida com sucesso!");
        View::redirect('/admin/viacoes');
    }

    private function getUploadedLogo(): ?array
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return $_FILES['logo'];
    }
}