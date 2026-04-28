<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use Exception;

//Controller da Página Inicial
final class HomeController
{
    private ViacaoService $viacoes;

    public function __construct(?ViacaoService $viacoes = null)
    {
        $this->viacoes = $viacoes ?? new ViacaoService();
    }

    // Renderiza a página inicial exibindo as viações ativas.
    public function index(): void
    {
        $viacoesAtivas = [];
        $erroConexao = false;

        // Verifica o status do cache
        $cacheHit = \getCachedData('viacoes_ativas') !== null;

        try {
            $viacoesAtivas = $this->viacoes->all('', 'ativo', 'nome', 'ASC');
        } catch (Exception $e) {
            // Garante que a página não quebre caso o banco fique indisponível
            $erroConexao = true;
        }

        View::render('home', [
            'viacoesAtivas' => $viacoesAtivas,
            'erroConexao'   => $erroConexao,
            'cacheHit'      => $cacheHit
        ]);
    }
}