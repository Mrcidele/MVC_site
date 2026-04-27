<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Services\ViacaoService;
use Exception;

final class HomeController
{
    private ViacaoService $viacoes;

    public function __construct(?ViacaoService $viacoes = null)
    {
        $this->viacoes = $viacoes ?? new ViacaoService();
    }

    public function index(): void
    {
        $viacoesAtivas = [];
        $erroConexao = false;

        try {
            $viacoesAtivas = $this->viacoes->all('', 'ativo', 'nome', 'ASC');
        } catch (Exception $e) {
            $erroConexao = true;
        }

        View::render('home', [
            'viacoesAtivas' => $viacoesAtivas,
            'erroConexao' => $erroConexao
        ]);
    }
}