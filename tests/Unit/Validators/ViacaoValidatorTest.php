<?php
declare(strict_types=1);

namespace Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use App\Validators\ViacaoValidator;

class ViacaoValidatorTest extends TestCase
{
    public function testDeveRetornarErroSeCamposObrigatoriosEstiveremVazios()
    {
        // Nome e cidade estão vazios de propósito
        $dadosInvalidos = [
            'nome' => '',
            'url' => 'https://exemplo.com',
            'cidade' => ''
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosInvalidos);

        // Afirmamos que o retorno é um array e não está vazio
        $this->assertIsArray($erros);
        $this->assertNotEmpty($erros);
        // Verificamos se a mensagem de erro esperada está no array retornado
        $this->assertContains('Preencha todos os campos obrigatórios.', $erros);
    }

    public function testDeveRetornarErroSeUrlForInvalida()
    {
        // Campos obrigatórios preenchidos, mas a URL não é um link válido
        $dadosInvalidos = [
            'nome' => 'Viação Exemplo',
            'url' => 'isso-nao-e-uma-url',
            'cidade' => 'São Paulo'
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosInvalidos);

        $this->assertIsArray($erros);
        $this->assertNotEmpty($erros);
        $this->assertContains('Forneça uma URL válida (ex: https://site.com).', $erros);
    }

    public function testDevePassarSemErrosQuandoDadosForemValidos()
    {
        // Todos os dados certinhos
        $dadosValidos = [
            'nome' => 'Viação Exemplo',
            'url' => 'https://exemplo.com.br',
            'cidade' => 'Curitiba'
        ];

        $validator = new ViacaoValidator();
        $erros = $validator->validate($dadosValidos);

        // Se os dados são válidos, o array de erros deve estar vazio
        $this->assertIsArray($erros);
        $this->assertEmpty($erros, 'O validador não deveria ter retornado erros.');
    }
}