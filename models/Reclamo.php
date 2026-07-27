<?php
/**
 * Modelo Reclamo: reclamos que un cliente levanta sobre una propiedad asociada a él.
 */

class Reclamo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lista paginada. $filters puede traer 'cliente_id' (para que el cliente vea
     * solo los suyos) o 'agente_id' (para que el agente vea solo los de sus propiedades).
     */
    public function paginated(int $limit, int $offset, array $filters = []): array
    {
        [$whereSql, $params] = $this->buildWhere($filters);

        $sql = "SELECT r.*, p.titulo AS property_titulo, u.nombre AS cliente_nombre
                FROM reclamos r
                JOIN properties p ON p.id = r.property_id
                JOIN users u ON u.id = r.cliente_id
                $whereSql
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        [$whereSql, $params] = $this->buildWhere($filters);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reclamos r
             JOIN properties p ON p.id = r.property_id
             $whereSql"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, p.titulo AS property_titulo, p.agente_id, u.nombre AS cliente_nombre
             FROM reclamos r
             JOIN properties p ON p.id = r.property_id
             JOIN users u ON u.id = r.cliente_id
             WHERE r.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $reclamo = $stmt->fetch();
        return $reclamo ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reclamos (property_id, cliente_id, titulo, descripcion)
             VALUES (:property_id, :cliente_id, :titulo, :descripcion)'
        );
        $stmt->execute([
            'property_id' => $data['property_id'],
            'cliente_id'  => $data['cliente_id'],
            'titulo'      => $data['titulo'],
            'descripcion' => $data['descripcion'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /** El agente/admin actualiza estado y puede dejar una respuesta */
    public function updateEstado(int $id, string $estado, ?string $respuesta): void
    {
        $stmt = $this->db->prepare(
            'UPDATE reclamos SET estado = :estado, respuesta = :respuesta WHERE id = :id'
        );
        $stmt->execute(['estado' => $estado, 'respuesta' => $respuesta, 'id' => $id]);
    }

    public function countAbiertos(array $filters = []): int
    {
        $filters['estado'] = 'abierto';
        [$whereSql, $params] = $this->buildWhere($filters);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reclamos r JOIN properties p ON p.id = r.property_id $whereSql"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['cliente_id'])) {
            $where[] = 'r.cliente_id = :cliente_id';
            $params['cliente_id'] = $filters['cliente_id'];
        }
        if (!empty($filters['agente_id'])) {
            $where[] = 'p.agente_id = :agente_id';
            $params['agente_id'] = $filters['agente_id'];
        }
        if (!empty($filters['estado'])) {
            $where[] = 'r.estado = :estado';
            $params['estado'] = $filters['estado'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return [$whereSql, $params];
    }
}
