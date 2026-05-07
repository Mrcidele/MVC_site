<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Repositories\HistoricoRepository;
use App\Services\AuthService;

// Controlador: Lida com a página de visualização do histórico.
final class HistoricoController {
    private HistoricoRepository $repo;
    private AuthService $auth;

    public function __construct() {
        $this->auth = new AuthService();

        // Segurança: Impede acesso de utilizadores não autenticados
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }

        $this->repo = new HistoricoRepository();
    }

    // Carrega a interface do histórico e processa os parâmetros de pesquisa da URL
    public function index(): void {
        $busca = trim((string)($_GET['busca'] ?? ''));
        $acao = trim((string)($_GET['acao'] ?? ''));

        // Envia os dados encapsulados em objetos para a interface
        View::render('admin/historico/index', [
            'historico' => $this->repo->all($busca, $acao),
            'filtros'   => compact('busca', 'acao')
        ]);
    }
}