<?php
declare(strict_types=1);
namespace App\Validators;

// Validador de Domínio: Garante a integridade dos dados da Viação.
final class ViacaoValidator
{
    // Valida os campos obrigatórios e o formato da URL.
    // Retorna array vazio se os dados forem válidos.
    public function validate(array $data): array
    {
        $errors = [];
        $nome = trim((string) ($data['nome'] ?? ''));
        $url = trim((string) ($data['url'] ?? ''));
        $cidade = trim((string) ($data['cidade'] ?? ''));

        if ($nome === '' || $url === '' || $cidade === '') {
            $errors[] = 'Preencha todos os campos obrigatórios.';
            return $errors;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Forneça uma URL válida (ex: https://site.com).';
        }

        return $errors;
    }
}