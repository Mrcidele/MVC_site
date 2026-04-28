<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\Viacao;
use PDO;

// Repositório: Responsável exclusivamente pela persistência de dados no MySQL.
final class ViacaoRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo = null) {
        $this->pdo = $pdo ?? \getPDO();
    }

    // Busca filtrada e ordenada.
    public function all(string $busca, string $status, string $ordem, string $dir): array{
        $sql = "SELECT * FROM viacoes WHERE 1=1";
        $params = [];

        if ($busca !== '') {
            $sql .= " AND nome LIKE :nome";
            $params['nome'] = "%$busca%";
        }
        if (in_array($status, ['ativo', 'inativo'], true)) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        // Whitelist de segurança para evitar SQL Injection via ORDER BY
        $colunas = ['id', 'nome', 'criado_em', 'alterado_em'];
        $ordem = in_array($ordem, $colunas) ? $ordem : 'nome';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY $ordem $dir";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(fn($r) => Viacao::fromRow($r), $stmt->fetchAll());
    }

    public function find(int $id): ?Viacao{
        $stmt = $this->pdo->prepare("SELECT * FROM viacoes WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Viacao::fromRow($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO viacoes (nome, url, cidade, status, logo) VALUES (:nome, :url, :cidade, :status, :logo)");
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }
    public function update(int $id, array $data): void
    {
        $sql = "UPDATE viacoes SET nome = :nome, url = :url, cidade = :cidade, status = :status";
        if (array_key_exists('logo', $data)) {
            $sql .= ", logo = :logo";
        } else {
            unset($data['logo']);
        }
        $sql .= " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM viacoes WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
