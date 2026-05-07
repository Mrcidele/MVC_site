<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Models\Usuario;
use PDO;

class UsuarioRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        // Ao invés de fazer require do arquivo, usamos a função global já carregada
        $this->db = $db ?? \getPdo();
    }

    public function findByEmail(string $email): ?Usuario
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Usuario::fromRow($row);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->execute([
            'nome'  => $data['nome'],
            'email' => $data['email'],
            'senha' => password_hash($data['senha'], PASSWORD_DEFAULT)
        ]);

        return (int) $this->db->lastInsertId();
    }
}