<?php
use App\Controllers\HomeController;
use App\Controllers\ViacaoController;
use App\Controllers\HistoricoController;

/** @var \App\Core\Router $router */

// Home
$router->get('/', [HomeController::class, 'index']);

// Admin - Viações (CRUD)
$router->get('/admin/viacoes', [ViacaoController::class, 'index']);
$router->get('/admin/viacoes/create', [ViacaoController::class, 'create']);
$router->post('/admin/viacoes', [ViacaoController::class, 'store']);
$router->get('/admin/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/admin/viacoes/{id}', [ViacaoController::class, 'update']);
$router->delete('/admin/viacoes/{id}', [ViacaoController::class, 'destroy']);

// Admin - Histórico
$router->get('/admin/historico', [HistoricoController::class, 'index']);