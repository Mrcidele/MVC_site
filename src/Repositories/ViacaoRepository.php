<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Models\Viacao;
use PDO;

// Repositório: Responsável exclusivamente pela persistência de dados no MySQL.
// NÃO deve conter regras de negócio (isso fica no Service), apenas Queries SQL.
class ViacaoRepository {
    private PDO $pdo;

    // Injeção de Dependência do PDO. Facilita testes (Mock) e centraliza a conexão.
    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? \getPdo();
    }

    // Busca filtrada e ordenada. Retorna um array de OBJETOS Viacao.
    public function all(string $busca, string $status, string $ordem = 'nome', string $dir = 'ASC'): array {
        $sql = "SELECT * FROM viacoes WHERE 1=1"; // 1=1 facilita a concatenação de "AND" dinâmicos
        $params = [];

        // Filtro de Busca (Nome ou Cidade)
        if ($busca !== '') {
            $sql .= " AND (nome LIKE :nome OR cidade LIKE :cidade)";
            $params['nome'] = "%$busca%";
            $params['cidade'] = "%$busca%";
        }

        // Filtro de Status exato
        if (in_array($status, ['ativo', 'inativo'], true)) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        // Whitelist de segurança: Impede ataques de SQL Injection no ORDER BY,
        // garantindo que o usuário só pode ordenar por colunas permitidas.
        $colunasPermitidas = ['id', 'nome', 'criado_em', 'alterado_em'];
        $ordem = in_array($ordem, $colunasPermitidas) ? $ordem : 'nome';
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY $ordem $dir";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // O array_map passa por cada linha do banco e a transforma em um Objeto Viacao
        return array_map(fn($r) => Viacao::fromRow($r), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Busca um registro específico pelo ID
    public function find(int $id): ?Viacao {
        $stmt = $this->pdo->prepare("SELECT * FROM viacoes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retorna o objeto Viacao se encontrar, ou "null" se o ID não existir
        return $row ? Viacao::fromRow($row) : null;
    }

    // Insere os dados e retorna o ID gerado pelo banco
    public function create(array $data): int {
        $stmt = $this->pdo->prepare("INSERT INTO viacoes (nome, url, cidade, status, logo) VALUES (:nome, :url, :cidade, :status, :logo)");
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    // Atualiza um registro
    public function update(int $id, array $data): void {
        $sql = "UPDATE viacoes SET nome = :nome, url = :url, cidade = :cidade, status = :status";

        // Só atualiza o campo de imagem (logo) se o usuário enviou uma imagem nova
        if (isset($data['logo'])) {
            $sql .= ", logo = :logo";
        }

        $sql .= " WHERE id = :id";
        $data['id'] = $id;
        $this->pdo->prepare($sql)->execute($data);
    }

    // Remove do banco de forma definitiva
    public function delete(int $id): void {
        $this->pdo->prepare("DELETE FROM viacoes WHERE id = :id")->execute(['id' => $id]);
    }
}