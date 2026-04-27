<?php
declare(strict_types=1);
namespace App\Validators;

final class ViacaoValidator
{
    public function validate(array $data): array
    {
        $errors = [];
        $nome = trim((string) ($data['nome'] ?? ''));
        $url = trim((string) ($data['url'] ?? ''));
        $cidade = trim((string) ($data['cidade'] ?? ''));

        if ($nome === '' || $url === '' || $cidade === '') {
            $errors[] = 'Preencha todos os campos obrigatórios.';
        }

        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Forneça uma URL válida (ex: https://site.com).';
        }

        return $errors;
    }
}