<?php
declare(strict_types=1);
namespace App\Models;

// Entidade Viacao: Representa o objeto de negócio imutável.
final class Viacao
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $url,
        public string $cidade,
        public string $status,
        public ?string $logo,
        public ?string $criadoEm,
        public ?string $alteradoEm,
    ) {}

    // Converte o array bruto do PDO em um objeto tipado.
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nome: (string) $row['nome'],
            url: (string) $row['url'],
            cidade: (string) $row['cidade'],
            status: (string) $row['status'],
            logo: $row['logo'] ?? null,
            criadoEm: $row['criado_em'] ?? null,
            alteradoEm: $row['alterado_em'] ?? null,
        );
    }
}