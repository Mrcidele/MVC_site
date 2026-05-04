<?php
declare(strict_types=1);

// Configurações de infraestrutura
define('CACHE_DIR', dirname(__DIR__) . '/cache/');
define('CACHE_TTL', 300);

// Singleton PDO: Garante uma única conexão TCP por requisição.
function getPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = 'db';
    $db = 'viacoes';
    $user = 'app';
    $pass = 'app123';
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

    // Sem o try/catch isolado aqui, qualquer erro de conexão (PDOException)
    // será automaticamente capturado pelo set_exception_handler do index.php.
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("SET time_zone='-03:00'");
    return $pdo;
}

function abrirConexao(): PDO {
    return getPdo();
}

// SISTEMA DE CACHE EM ARQUIVO.
// Recupera dados do cache JSON.
function getCachedData(string $key): ?array {
    $file = CACHE_DIR . $key . '.json';
    if (!file_exists($file)) return null;
    if ((time() - filemtime($file)) > CACHE_TTL) return null;

    return json_decode(file_get_contents($file), true);
}

// Salva dados em cache JSON.
function setCachedData(string $key, array $data): void {
    if (!is_dir(CACHE_DIR)) mkdir(CACHE_DIR, 0755, true);
    file_put_contents(CACHE_DIR . $key . '.json', json_encode($data, JSON_UNESCAPED_UNICODE));
}

// Remove o cache (Invalidação).
function invalidateCache(string $key): void {
    $file = CACHE_DIR . $key . '.json';
    if (file_exists($file)) unlink($file);
}