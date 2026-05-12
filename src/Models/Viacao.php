<?php
declare(strict_types=1);
namespace App\Models;
class Viacao {


    public function __construct(
        public ?int $id,
        public string $nome,
        public string $url,
        public string $cidade,
        public string $status,
        public ?string $logo,
        public ?string $criado_em = null,
        public ?string $alterado_em = null
    ) {}

    public static function fromRow(array $row): self {
        return new self(
            (int)$row['id'],
            $row['nome'],
            $row['url'],
            $row['cidade'],
            $row['status'],
            $row['logo'],
            $row['criado_em'] ?? null,
            $row['alterado_em'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'url' => $this->url,
            'cidade' => $this->cidade,
            'status' => $this->status,
            'logo' => $this->logo,
            'criado_em' => $this->criado_em,
            'alterado_em' => $this->alterado_em
        ];
    }
}