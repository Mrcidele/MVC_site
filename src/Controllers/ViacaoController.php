<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use App\Services\AuthService;
use Exception;

// Controller Administrativo para Gestão de Viações.
// Objetivo: Receber as requisições HTTP (GET, POST), delegar o trabalho pesado
// para a camada de Serviço (ViacaoService) e enviar o resultado para a View.
final class ViacaoController
{
    private ViacaoService $service;
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();

        // TRAVA DE SEGURANÇA: Bloqueia acesso não autorizado
        // Se a sessão de login for falsa, expulsa o usuário da área administrativa
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }

        $this->service = new ViacaoService();
    }

    // Listagem principal de viações
    public function index(): void
    {
        // Captura os dados da URL (?nome=X&status=Y).
        // O (string) garante que não quebraremos o código se for passado um array por engano.
        $busca  = (string)($_GET['nome'] ?? '');
        $status = (string)($_GET['status'] ?? '');
        $ordem  = (string)($_GET['order'] ?? 'criado_em'); // Por padrão, lista os mais recentes primeiro
        $dir    = (string)($_GET['dir'] ?? 'DESC');

        // Chama a renderização da tela, injetando os dados que vieram do Service
        View::render('admin/viacoes/index', [
            'viacoes' => $this->service->all($busca, $status, $ordem, $dir),
            'filtros' => compact('busca', 'status', 'ordem', 'dir')
        ]);
    }

    // Carrega o formulário para adicionar uma nova viação
    public function create(): void
    {
        View::render('admin/viacoes/create', [
            'errors' => [],
            'old' => ['nome' => '', 'url' => '', 'cidade' => '', 'status' => 'ativo'] // Evita erro de variável não definida na View
        ]);
    }

    // Processa o envio do formulário de criação (Rota POST)
    public function store(): void
    {
        try {
            // Tenta criar passando os dados de texto ($_POST) e arquivos de imagem ($_FILES)
            $this->service->create($_POST, $_FILES['logo'] ?? null);
            header('Location: /admin/viacoes'); // Se der certo, volta pra lista
            exit;
        } catch (Exception $e) {
            // Se o Service lançar uma Exceção (erro de upload, validação, etc),
            // captura o erro, divide a mensagem e devolve o usuário pro formulário
            View::render('admin/viacoes/create', [
                'errors' => explode('|', $e->getMessage()),
                'old' => $_POST // Preenche os inputs com o que ele tinha digitado antes do erro
            ]);
        }
    }

    // Carrega o formulário de edição já preenchido
    public function edit(int $id): void
    {
        // Busca o objeto específico pelo ID passado na URL
        $viacao = $this->service->find($id);

        // Se tentarem editar um ID que não existe (ex: digitou na URL), redireciona
        if (!$viacao) {
            header('Location: /admin/viacoes');
            exit;
        }

        View::render('admin/viacoes/edit', [
            'viacao' => $viacao,
            'errors' => [],
            'old' => [
                'nome' => $viacao->nome,
                'url' => $viacao->url,
                'cidade' => $viacao->cidade,
                'status' => $viacao->status
            ]
        ]);
    }

    // Processa a atualização dos dados (Rota POST / PUT)
    public function update(int $id): void
    {
        try {
            $this->service->update($id, $_POST, $_FILES['logo'] ?? null);
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

    // Processa a exclusão de um registro (Rota DELETE)
    public function destroy(int $id): void
    {
        // Chama o serviço para apagar. A lógica de histórico de quem apagou fica oculta no Service.
        $this->service->delete($id);
        header('Location: /admin/viacoes');
        exit;
    }
}