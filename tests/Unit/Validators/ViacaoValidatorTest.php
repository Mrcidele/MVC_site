<?php
declare(strict_types=1);

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Validators\ViacaoValidator;

class ViacaoValidatorTest extends TestCase
{
    public function testDeveRetornarErroSeCamposObrigatoriosEstiveremVazios()
    {
        $dadosInvalidos = [
            'nome' => '   ',
            'url' => 'https://exemplo.com',
            'cidade' => ''
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosInvalidos);

        $this->assertIsArray($erros);
        $this->assertNotEmpty($erros);
        // Atualizado para a mensagem real que sua classe emite:
        $this->assertContains('O nome da viação é obrigatório.', $erros);
        $this->assertContains('A cidade é obrigatória.', $erros);
    }

    // Atualizado para o formato moderno do PHPUnit 10/11+
    #[DataProvider('provedorDeUrlsInvalidas')]
    public function testDeveRetornarErroSeUrlForInvalida(string $urlInvalida)
    {
        $dadosInvalidos = [
            'nome' => 'Viação Exemplo',
            'url' => $urlInvalida,
            'cidade' => 'São Paulo',
            'status' => 'ativo' // Adicionado o status para não dar erro de status
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosInvalidos);

        $this->assertIsArray($erros);
        $this->assertNotEmpty($erros);
        // Atualizado para a mensagem real da sua classe
        $this->assertContains('A URL fornecida possui um formato inválido.', $erros);
    }

    public static function provedorDeUrlsInvalidas(): array
    {
        return [
            ['isso-nao-e-uma-url'],
            ['www.sem-http.com.br'],
            ['http://'],
            ['javascript:alert("xss")'],
        ];
    }

    public function testDevePassarSemErrosQuandoDadosForemValidos()
    {
        $dadosValidos = [
            'nome' => 'Viação Exemplo',
            'url' => 'https://exemplo.com.br',
            'cidade' => 'Curitiba',
            'status' => 'ativo' // Adicionado o status que estava faltando!
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosValidos);

        $this->assertIsArray($erros);
        $this->assertEmpty($erros, 'O validador não deveria ter retornado erros.');
    }
}