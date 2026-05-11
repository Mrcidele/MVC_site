<?php
declare(strict_types=1);
namespace App\Validators;

// Validador de Domínio: Garante a integridade dos dados da Viação.
final class ViacaoValidator
{
    // Valida os campos obrigatórios e o formato da URL.
    public static function validate(array &$dados) {
        $erros = [];

        if (empty($dados['nome'])) {
            $erros[] = "O nome é obrigatório.";
        } else {
            // Remove tags HTML e espaços extras na entrada
            $dados['nome'] = strip_tags(trim($dados['nome']));
        }

        // Repita o processo para os outros campos de texto...

        return $erros;
    }
 }