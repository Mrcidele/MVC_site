<?php
declare(strict_types=1);
namespace App\Core;

// Camada de Apresentação: Gerencia renderização, redirecionamentos e mensagens de sessão.
final class View
{
    // Renderiza um template PHP e extrai dados para variáveis locais.
    public static function render(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View não encontrada: ' . htmlspecialchars($view);
            return;
        }

        // Transforma chaves do array em variáveis (ex: ['nome' => 'Azul'] vira $nome).
        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    // Redireciona o usuário e interrompe a execução do script.
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    // Define uma mensagem flash (persiste por apenas um redirecionamento).
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // Consome e remove a mensagem flash da sessão.
    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }
}