<?php
use App\Controllers\HomeController;
use App\Controllers\ViacaoController;

// Home
$router->get('/', [HomeController::class, 'index']);

// Admin
$router->get('/admin/viacoes', [ViacaoController::class, 'index']);
$router->get('/admin/viacoes/create', [ViacaoController::class, 'create']);
$router->post('/admin/viacoes', [ViacaoController::class, 'store']);
$router->get('/admin/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/admin/viacoes/{id}', [ViacaoController::class, 'update']);
$router->delete('/admin/viacoes/{id}', [ViacaoController::class, 'destroy']);