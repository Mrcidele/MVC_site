<?php
declare(strict_types=1);

namespace Tests\Unit\DTOs;

use PHPUnit\Framework\TestCase;
use App\DTOs\ViacaoDTO;

class ViacaoDTOTest extends TestCase
{
    public function testDeveCriarDtoAPartirDoRequestCorretamente()
    {
        // Simulando a superglobal $_POST
        $postData = [
            'nome' => 'Viação Cometa',
            'url' => 'https://cometa.com.br',
            'cidade' => 'São Paulo',
            'status' => 'ativo'
        ];

        // Simulando a superglobal $_FILES (sem upload de imagem)
        $fileData = null;

        $dto = ViacaoDTO::fromRequest($postData, $fileData);

        // Verificando se os dados foram injetados corretamente no DTO
        $this->assertEquals('Viação Cometa', $dto->nome);
        $this->assertEquals('https://cometa.com.br', $dto->url);
        $this->assertEquals('São Paulo', $dto->cidade);
        $this->assertEquals('ativo', $dto->status);
        $this->assertNull($dto->logoFile);
    }

    public function testDeveLidarComDadosVaziosNoRequest()
    {
        // Mandando um array vazio (como se o usuário submetesse o form em branco)
        $postData = [];

        $dto = ViacaoDTO::fromRequest($postData, null);

        // O DTO deve atribuir strings vazias ao invés de dar erro de "undefined array key"
        $this->assertSame('', $dto->nome);
        $this->assertSame('', $dto->url);
        $this->assertSame('', $dto->cidade);
        $this->assertSame('ativo', $dto->status);
    }
}