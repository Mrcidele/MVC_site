<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\View;
use App\Repositories\HistoricoRepository;

final class HistoricoController
{
    public function index(): void
    {
        $repo = new HistoricoRepository();
        $historico = $repo->all();

        View::render('admin/historico/index', [
            'historico' => $historico
        ]);
    }
}