<?php
declare(strict_types=1);

function getPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = 'db';
    $db = 'viacoes';
    $user = 'app';
    $pass = 'app123';
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $pdo->exec("SET time_zone='-03:00'");
        return $pdo;
    } catch (PDOException $e) {
        die('Erro na conexão com o banco: ' . $e->getMessage());
    }
}