<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ViacaoService;
use App\Repositories\ViacaoRepository;
use App\Repositories\HistoricoRepository;
use App\Services\AuthService;
use App\Models\Viacao;

class ViacaoServiceTest extends TestCase
{
    public function testDeveEncontrarUmaViacaoPeloId()
    {
        // Mockamos todas as dependências que encostam no banco ou na sessão
        $mockRepo = $this->createMock(ViacaoRepository::class);
        $stubHistorico = $this->createStub(HistoricoRepository::class);
        $stubAuth = $this->createStub(AuthService::class);

        $viacaoFake = new Viacao(99, 'Viação Fake', 'http://fake.com', 'Curitiba', 'ativo', null);
        $mockRepo->expects($this->once())
            ->method('find')
            ->with(99)
            ->willReturn($viacaoFake);

        // Injetamos os mocks em vez de null
        $service = new ViacaoService(
            $mockRepo,
            null, // validator não encosta no banco, não precisa de mock
            $stubHistorico,
            $stubAuth
        );

        $resultado = $service->find(99);

        $this->assertInstanceOf(Viacao::class, $resultado);
        $this->assertEquals('Viação Fake', $resultado->nome);
        $this->assertEquals(99, $resultado->id);
    }

    public function testDeveRetornarNullSeViacaoNaoExistir()
    {
        $mockRepo = $this->createMock(ViacaoRepository::class);
        $stubHistorico = $this->createStub(HistoricoRepository::class);
        $stubAuth = $this->createStub(AuthService::class);

        $mockRepo->expects($this->once())
            ->method('find')
            ->with(10)
            ->willReturn(null);

        $service = new ViacaoService($mockRepo, null, $stubHistorico, $stubAuth);
        $resultado = $service->find(10);

        $this->assertNull($resultado);
    }
}