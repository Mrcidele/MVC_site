<?php
declare(strict_types=1);
namespace App\Repositories;

use PDO;

class HistoricoRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? \getPdo();
    }

    /**
     * Registra uma nova ação no histórico.
     * Chamado pelo ViacaoService ao criar, editar ou excluir.
     */
    public function log(int $viacaoId, string $acao, string $detalhes, ?int $usuarioId = null): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO viacoes_historico (viacao_id, usuario_id, acao, detalhes) 
            VALUES (:viacao_id, :usuario_id, :acao, :detalhes)
        ");

        $stmt->execute([
            'viacao_id'  => $viacaoId,
            'usuario_id' => $usuarioId,
            'acao'       => $acao,
            'detalhes'   => $detalhes
        ]);
    }

    /**
     * Busca o histórico com filtros (usado na listagem administrativa).
     */
    public function all(string $busca = '', string $acao = ''): array
    {
        $sql = "
            SELECT 
                h.*, 
                u.nome as usuario_nome, 
                v.nome as viacao_nome 
            FROM viacoes_historico h
            LEFT JOIN usuarios u ON h.usuario_id = u.id
            LEFT JOIN viacoes v ON h.viacao_id = v.id
            WHERE 1=1
        ";

        $params = [];

        if ($busca !== '') {
            $sql .= " AND (v.nome LIKE :busca OR h.detalhes LIKE :busca OR u.nome LIKE :busca)";
            $params['busca'] = "%$busca%";
        }

        if ($acao !== '') {
            $sql .= " AND h.acao = :acao";
            $params['acao'] = $acao;
        }

        $sql .= " ORDER BY h.data_hora DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    //Busca o Historico de uma viação especifica
    public function findByViacaoId(int $viacaoId): array
    {
        $sql = "
            SELECT 
                h.*, 
                u.nome as usuario_nome
            FROM viacoes_historico h
            LEFT JOIN usuarios u ON h.usuario_id = u.id
            WHERE h.viacao_id = :viacao_id
            ORDER BY h.data_hora DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['viacao_id' => $viacaoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}