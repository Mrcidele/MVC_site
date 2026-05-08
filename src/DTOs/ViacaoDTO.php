<?php
declare(strict_types=1);

namespace App\DTOs;


 // Data Transfer Object para Viação.
 // Centraliza e tipa os dados vindos da requisição.

readonly class ViacaoDTO
{
    public function __construct(
        public string $nome,
        public string $url,
        public string $cidade,
        public string $status,
        public ?array $logoFile = null
    ) {}


     // Factory method para criar o DTO a partir das globais do PHP.

    public static function fromRequest(array $postData, ?array $fileData): self
    {
        return new self(
            nome: trim($postData['nome'] ?? ''),
            url: trim($postData['url'] ?? ''),
            cidade: trim($postData['cidade'] ?? ''),
            status: ($postData['status'] ?? '') === 'inativo' ? 'inativo' : 'ativo',
            logoFile: ($fileData && $fileData['error'] === UPLOAD_ERR_OK) ? $fileData : null
        );
    }


     // Converte os dados para array (útil para validadores e repositórios antigos).

    public function toArray(): array
    {
        return [
            'nome'   => $this->nome,
            'url'    => $this->url,
            'cidade' => $this->cidade,
            'status' => $this->status
        ];
    }
}