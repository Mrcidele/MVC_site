<?php
declare(strict_types=1);
namespace App\Validators;

// Validador de Domínio: Garante a integridade dos dados da Viação e previne XSS.
final class ViacaoValidator
{
    /**
     * Valida e sanitiza os campos da Viação.
     * O parâmetro &$dados é passado por referência para que a sanitização
     * reflita diretamente no array original (DTO convertido) usado para persistência.
     */
    public function validate(array &$dados): array
    {
        $erros = [];

        // 1. Sanitização (Defesa em Profundidade contra XSS)
        // Remove espaços extras nas pontas e destrói qualquer tag HTML/Script injetada
        if (isset($dados['nome']))   $dados['nome']   = strip_tags(trim($dados['nome']));
        if (isset($dados['cidade'])) $dados['cidade'] = strip_tags(trim($dados['cidade']));
        if (isset($dados['url']))    $dados['url']    = strip_tags(trim($dados['url']));
        if (isset($dados['status'])) $dados['status'] = strip_tags(trim($dados['status']));

        // 2. Validações Estruturais e de Negócio
        if (empty($dados['nome'])) {
            $erros[] = "O nome da viação é obrigatório.";
        }

        if (empty($dados['cidade'])) {
            $erros[] = "A cidade é obrigatória.";
        }

        if (empty($dados['url'])) {
            $erros[] = "A URL do site é obrigatória.";
        } elseif (!filter_var($dados['url'], FILTER_VALIDATE_URL)) {
            $erros[] = "A URL fornecida possui um formato inválido.";
        }

        if (empty($dados['status'])) {
            $erros[] = "O status é obrigatório.";
        } elseif (!in_array($dados['status'], ['ativo', 'inativo'])) {
            $erros[] = "O status fornecido é inválido. Escolha 'ativo' ou 'inativo'.";
        }

        return $erros;
    }
}