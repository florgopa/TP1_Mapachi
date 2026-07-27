<?php
/**
 * Modelo Property: CRUD de propiedades + consultas filtradas por rol.
 */

class Property
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lista paginada de propiedades, con filtros opcionales.
     * $scope permite restringir resultados según el rol que consulta:
     *  - null           -> todas (uso público / admin)
     *  - ['agente_id']  -> solo las que gestiona ese agente
     *  - ['cliente_id'] -> solo las asociadas a ese cliente (propietario)
     */
    public function paginated(int $limit, int $offset, array $filters = []): array
    {
        [$whereSql, $params] = $this->buildWhere($filters);
        $orderSql = $this->buildOrder($filters['orden'] ?? '');

        $sql = "SELECT p.*, a.nombre AS agente_nombre, c.nombre AS propietario_nombre
                FROM properties p
                JOIN users a ON a.id = p.agente_id
                LEFT JOIN users c ON c.id = p.propietario_id
                $whereSql
                $orderSql
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
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM properties p $whereSql");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /** Cantidad de propiedades agrupadas por estado (para el dashboard) */
    public function countByEstado(array $filters = []): array
    {
        [$whereSql, $params] = $this->buildWhere($filters);
        $stmt = $this->db->prepare("SELECT estado, COUNT(*) AS total FROM properties p $whereSql GROUP BY estado");
        $stmt->execute($params);

        $result = ['disponible' => 0, 'reservada' => 0, 'alquilada' => 0, 'vendida' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['estado']] = (int) $row['total'];
        }
        return $result;
    }

    /** Cantidad de propiedades agrupadas por tipo (para el dashboard) */
    public function countByTipo(array $filters = []): array
    {
        [$whereSql, $params] = $this->buildWhere($filters);
        $stmt = $this->db->prepare("SELECT tipo, COUNT(*) AS total FROM properties p $whereSql GROUP BY tipo");
        $stmt->execute($params);

        $result = ['venta' => 0, 'alquiler' => 0, 'pozo' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['tipo']] = (int) $row['total'];
        }
        return $result;
    }

    /** Arma el WHERE + params compartido entre paginated() y count() */
    private function buildWhere(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['tipo'])) {
            $where[] = 'p.tipo = :tipo';
            $params['tipo'] = $filters['tipo'];
        }
        if (!empty($filters['zona'])) {
            $where[] = 'p.zona LIKE :zona';
            $params['zona'] = '%' . $filters['zona'] . '%';
        }
        if (!empty($filters['agente_id'])) {
            $where[] = 'p.agente_id = :agente_id';
            $params['agente_id'] = $filters['agente_id'];
        }
        if (!empty($filters['propietario_id'])) {
            $where[] = 'p.propietario_id = :propietario_id';
            $params['propietario_id'] = $filters['propietario_id'];
        }
        if (!empty($filters['precio_min'])) {
            $where[] = 'p.precio >= :precio_min';
            $params['precio_min'] = $filters['precio_min'];
        }
        if (!empty($filters['precio_max'])) {
            $where[] = 'p.precio <= :precio_max';
            $params['precio_max'] = $filters['precio_max'];
        }
        if (!empty($filters['dormitorios'])) {
            $where[] = 'p.dormitorios >= :dormitorios';
            $params['dormitorios'] = $filters['dormitorios'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return [$whereSql, $params];
    }

    /** Traduce el parámetro de orden a un ORDER BY seguro (whitelist, nunca interpolando el input directo) */
    private function buildOrder(string $orden): string
    {
        return match ($orden) {
            'precio_asc'  => 'ORDER BY p.precio ASC',
            'precio_desc' => 'ORDER BY p.precio DESC',
            default       => 'ORDER BY p.created_at DESC',
        };
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, a.nombre AS agente_nombre, c.nombre AS propietario_nombre
             FROM properties p
             JOIN users a ON a.id = p.agente_id
             LEFT JOIN users c ON c.id = p.propietario_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $property = $stmt->fetch();
        return $property ?: null;
    }

    /** Todas las propiedades asociadas a un cliente (sin paginar, para "Mis propiedades" y selects) */
    public function misPropiedades(int $clienteId): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, a.nombre AS agente_nombre
             FROM properties p
             JOIN users a ON a.id = p.agente_id
             WHERE p.propietario_id = :cliente_id
             ORDER BY p.created_at DESC'
        );
        $stmt->execute(['cliente_id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO properties
                (titulo, descripcion, tipo, precio, zona, direccion, dormitorios, banos, imagen, estado, agente_id, propietario_id)
             VALUES
                (:titulo, :descripcion, :tipo, :precio, :zona, :direccion, :dormitorios, :banos, :imagen, :estado, :agente_id, :propietario_id)'
        );
        $stmt->execute([
            'titulo'         => $data['titulo'],
            'descripcion'    => $data['descripcion'],
            'tipo'           => $data['tipo'],
            'precio'         => $data['precio'],
            'zona'           => $data['zona'],
            'direccion'      => $data['direccion'],
            'dormitorios'    => $data['dormitorios'],
            'banos'          => $data['banos'],
            'imagen'         => $data['imagen'] ?: null,
            'estado'         => $data['estado'],
            'agente_id'      => $data['agente_id'],
            'propietario_id' => $data['propietario_id'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = 'UPDATE properties SET
                    titulo = :titulo, descripcion = :descripcion, tipo = :tipo, precio = :precio,
                    zona = :zona, direccion = :direccion, dormitorios = :dormitorios, banos = :banos,
                    estado = :estado, agente_id = :agente_id, propietario_id = :propietario_id';
        $params = [
            'titulo'         => $data['titulo'],
            'descripcion'    => $data['descripcion'],
            'tipo'           => $data['tipo'],
            'precio'         => $data['precio'],
            'zona'           => $data['zona'],
            'direccion'      => $data['direccion'],
            'dormitorios'    => $data['dormitorios'],
            'banos'          => $data['banos'],
            'estado'         => $data['estado'],
            'agente_id'      => $data['agente_id'],
            'propietario_id' => $data['propietario_id'] ?: null,
            'id'             => $id,
        ];

        if (!empty($data['imagen'])) {
            $sql .= ', imagen = :imagen';
            $params['imagen'] = $data['imagen'];
        }

        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM properties WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
