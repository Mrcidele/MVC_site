<?php
declare(strict_types=1);

// Front Controller: O único ponto de entrada da aplicação.
// Responsável por preparar o ambiente e disparar o roteador.

// Define o ambiente atual (Mude para 'production' quando for colocar no ar)
    define('APP_ENV', 'development');

// Configuração de exibição nativa do PHP
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__, 2) . '/logs/php-error.log'); // Salva os erros em um arquivo de log
    error_reporting(E_ALL);
}

// Handler Global de Exceções
set_exception_handler(function (\Throwable $e) {
    http_response_code(500); // Força o status HTTP 500

    if (APP_ENV === 'development') {
        // Exibe o erro detalhado para o desenvolvedor
        echo "<h1>Erro Fatal</h1>";
        echo "<strong>Mensagem:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Arquivo:</strong> " . $e->getFile() . " na linha " . $e->getLine() . "<br><br>";
        echo "<strong>Stack Trace:</strong><pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        // Registra o erro no log e exibe mensagem amigável para o usuário final
        error_log($e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
        echo "<h1>Ops! Algo deu errado.</h1>";
        echo "<p>Ocorreu um erro interno no servidor. Nossa equipe já foi notificada. Por favor, tente novamente mais tarde.</p>";
    }
    exit;
});

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Core\Router;

// Inicializa a sessão para suporte a Flash Messages e Autenticação
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$router = new Router();

// Carrega a definição das rotas amigáveis
require_once dirname(__DIR__) . '/routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);