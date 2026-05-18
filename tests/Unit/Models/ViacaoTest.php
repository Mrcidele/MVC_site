<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Viacao;

class ViacaoTest extends TestCase
{
    public function testDeveCriarInstanciaDeViacaoCorretamente()
    {
        $viacao = new Viacao(
            1,
            'Viação Exemplo',
            'https://exemplo.com.br',
            'São Paulo',
            'ativo',
            'logo.png',
            '2023-10-01 10:00:00',
            null
        );

        $this->assertEquals(1, $viacao->id);
        $this->assertEquals('Viação Exemplo', $viacao->nome);
        $this->assertEquals('https://exemplo.com.br', $viacao->url);
        $this->assertEquals('São Paulo', $viacao->cidade);
        $this->assertEquals('ativo', $viacao->status);
    }

    public function testDeveCriarViacaoAPartirDoMetodoFromRow()
    {
        $row = [
            'id' => 2,
            'nome' => 'Viação Teste PDO',
            'url' => 'https://teste.com',
            'cidade' => 'Rio de Janeiro',
            'status' => 'inativo',
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

    // Garantir que a serialização para array funcione
    public function testDeveConverterModelParaArrayCorretamente()
    {
        $viacao = new Viacao(3, 'Viação Array', 'https://array.com', 'Curitiba', 'ativo', null);

        $array = $viacao->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertEquals(3, $array['id']);
        $this->assertEquals('Viação Array', $array['nome']);
        $this->assertNull($array['logo']);
    }
}