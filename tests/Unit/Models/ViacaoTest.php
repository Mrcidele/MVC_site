<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Viacao;

class ViacaoTest extends TestCase
{
    public function testDeveCriarInstanciaDeViacaoCorretamente()
    {
        // Passando os 8 argumentos exigidos pelo construtor do Model
        $viacao = new Viacao(
            1,
            'Viação Exemplo',
            'https://exemplo.com.br',
            'São Paulo',
            'Ativa',
            'logo.png',
            '2023-10-01 10:00:00',
            null
        );

        // Testando o model diretamente
        $this->assertEquals(1, $viacao->id);
        $this->assertEquals('Viação Exemplo', $viacao->nome);
        $this->assertEquals('https://exemplo.com.br', $viacao->url);
        $this->assertEquals('São Paulo', $viacao->cidade);
        $this->assertEquals('Ativa', $viacao->status);
    }

    public function testDeveCriarViacaoAPartirDoMetodoFromRow()
    {
        // Simulando o array que o PDO (banco de dados) retornaria
        $row = [
            'id' => 2,
            'nome' => 'Viação Teste PDO',
            'url' => 'https://teste.com',
            'cidade' => 'Rio de Janeiro',
            'status' => 'Inativa',
            'logo' => null,
            'criado_em' => '2023-10-01',
            'alterado_em' => null
        ];

        $viacao = Viacao::fromRow($row);

        $this->assertInstanceOf(Viacao::class, $viacao);
        $this->assertEquals(2, $viacao->id);
        $this->assertEquals('Viação Teste PDO', $viacao->nome);
        $this->assertNull($viacao->logo);
    }
}