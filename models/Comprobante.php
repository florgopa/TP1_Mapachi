<?php
/**
 * Modelo Comprobante: comprobantes de pago que sube el cliente sobre una propiedad asociada a él.
 */

class Comprobante
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function paginated(int $limit, int $offset, array $filters = []): array
    {
        [$whereSql, $params] = $this->buildWhere($filters);

        $sql = "SELECT c.*, p.titulo AS property_titulo, u.nombre AS cliente_nombre
                FROM comprobantes c
                JOIN properties p ON p.id = c.property_id
                JOIN users u ON u.id = c.cliente_id
                $whereSql
                ORDER BY c.created_at DESC
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
            "SELECT COUNT(*) FROM comprobantes c
             JOIN properties p ON p.id = c.property_id
             $whereSql"
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, p.titulo AS property_titulo, p.agente_id, u.nombre AS cliente_nombre
             FROM comprobantes c
             JOIN properties p ON p.id = c.property_id
             JOIN users u ON u.id = c.cliente_id
             WHERE c.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $comprobante = $stmt->fetch();
        return $comprobante ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO comprobantes (property_id, cliente_id, archivo, monto)
             VALUES (:property_id, :cliente_id, :archivo, :monto)'
        );
        $stmt->execute([
            'property_id' => $data['property_id'],
            'cliente_id'  => $data['cliente_id'],
            'archivo'     => $data['archivo'],
            'monto'       => $data['monto'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateEstado(int $id, string $estado, ?string $notaAdmin): void
    {
        $stmt = $this->db->prepare(
            'UPDATE comprobantes SET estado = :estado, nota_admin = :nota WHERE id = :id'
        );
        $stmt->execute(['estado' => $estado, 'nota' => $notaAdmin, 'id' => $id]);
    }

    private function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['cliente_id'])) {
            $where[] = 'c.cliente_id = :cliente_id';
            $params['cliente_id'] = $filters['cliente_id'];
        }
        if (!empty($filters['agente_id'])) {
            $where[] = 'p.agente_id = :agente_id';
            $params['agente_id'] = $filters['agente_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return [$whereSql, $params];
    }
}
