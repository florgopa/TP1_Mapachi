<?php
/**
 * Modelo User: acceso a datos de usuarios y roles.
 * Toda consulta usa sentencias preparadas (PDO) para evitar inyección SQL.
 */

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.nombre AS role_nombre
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email AND u.activo = 1
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT u.*, r.nombre AS role_nombre
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT u.*, r.nombre AS role_nombre
             FROM users u
             JOIN roles r ON r.id = u.role_id
             ORDER BY u.id DESC'
        );
        return $stmt->fetchAll();
    }

    /** Agentes activos, para asignar como responsables de una propiedad */
    public function agentes(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nombre FROM users WHERE role_id = 2 AND activo = 1 ORDER BY nombre"
        );
        return $stmt->fetchAll();
    }

    /** Clientes activos, para asignar como propietarios/inquilinos de una propiedad */
    public function clientes(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nombre FROM users WHERE role_id = 3 AND activo = 1 ORDER BY nombre"
        );
        return $stmt->fetchAll();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = :email';
        $params = ['email' => $email];
        if ($excludeId) {
            $sql .= ' AND id != :id';
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (nombre, email, password, telefono, role_id)
             VALUES (:nombre, :email, :password, :telefono, :role_id)'
        );
        $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'telefono' => $data['telefono'] ?: null,
            'role_id'  => $data['role_id'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE users SET nombre = :nombre, email = :email, telefono = :telefono, role_id = :role_id, activo = :activo';
        $params = [
            'nombre'  => $data['nombre'],
            'email'   => $data['email'],
            'telefono'=> $data['telefono'] ?: null,
            'role_id' => $data['role_id'],
            'activo'  => $data['activo'],
            'id'      => $id,
        ];

        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
