<?php
/**
 * Modelo Message: guarda y lista los mensajes del formulario de contacto público.
 */

class Message
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (nombre, email, telefono, mensaje) VALUES (:nombre, :email, :telefono, :mensaje)'
        );
        $stmt->execute([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'telefono' => $data['telefono'] ?: null,
            'mensaje'  => $data['mensaje'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function paginated(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare('SELECT * FROM messages ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM messages')->fetchColumn();
    }

    /** Cantidad de mensajes sin leer (para el dashboard) */
    public function countUnread(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM messages WHERE leido = 0')->fetchColumn();
    }

    public function markAsRead(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE messages SET leido = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM messages WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
