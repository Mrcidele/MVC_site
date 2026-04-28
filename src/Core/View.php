<?php
declare(strict_types=1);
namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View não encontrada: ' . htmlspecialchars($view);
            exit;
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}