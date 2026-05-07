<?php
declare(strict_types=1);
namespace App\Models;

final class Usuario
{
    public function __construct(
        public ?int $id = null,
        public string $nome = '',
        public string $email = '',
        public string $senha = '',
        public ?string $criadoEm = null
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) ($row['id'] ?? 0),
            nome: $row['nome'] ?? '',
            email: $row['email'] ?? '',
            senha: $row['senha'] ?? '',
            criadoEm: $row['criado_em'] ?? null
        );
    }
}