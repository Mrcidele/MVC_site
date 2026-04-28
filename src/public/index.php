<?php
declare(strict_types=1);

// Front Controller: O único ponto de entrada da aplicação.
// Responsável por preparar o ambiente e disparar o roteador.

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Router;

// Inicializa a sessão para suporte a Flash Messages e Autenticação
session_start();

$router = new Router();

// Carrega a definição das rotas amigáveis
require_once dirname(__DIR__) . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);