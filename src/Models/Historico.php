<?php
declare(strict_types=1);
namespace App\Models;

// Representa um registro de auditoria no sistema.
final class Historico
{
    public function __construct(
        public int $id,
        public ?int $viacaoId,
        public string $acao,
        public string $detalhes,
        public string $dataHora
    ) {}
}