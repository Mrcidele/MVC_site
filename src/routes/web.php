<?php
use App\Controllers\HomeController;
use App\Controllers\ViacaoController;
use App\Controllers\HistoricoController;
use App\Controllers\LoginController;
use App\Controllers\LogAcessoController; // IMPORTAÇÃO NOVA PARA O SOC

/** @var \App\Core\Router $router */

// Home - Acesso público
$router->get('/', [HomeController::class, 'index']);

// --- AUTENTICAÇÃO ---
// Exibe o formulário
$router->get('/login', [LoginController::class, 'index']);
// Processa o envio dos dados
$router->post('/login', [LoginController::class, 'authenticate']);
// Encerra a sessão
$router->get('/logout', [LoginController::class, 'sair']);

// --- ADMIN - VIAÇÕES (Protegido pelo construtor do ViacaoController) ---
$router->get('/admin/viacoes', [ViacaoController::class, 'index']);
$router->get('/admin/viacoes/create', [ViacaoController::class, 'create']);
$router->post('/admin/viacoes', [ViacaoController::class, 'store']);
$router->get('/admin/viacoes/{id}/edit', [ViacaoController::class, 'edit']);
$router->put('/admin/viacoes/{id}', [ViacaoController::class, 'update']);
$router->delete('/admin/viacoes/{id}', [ViacaoController::class, 'destroy']);

// --- ADMIN - HISTÓRICO (Protegido pelo construtor do HistoricoController) ---
$router->get('/admin/historico', [HistoricoController::class, 'index']);

// --- ADMIN - AUDITORIA SOC (LOGS DE ACESSO) ---
$router->get('/admin/logs-acesso', [LogAcessoController::class, 'index']);