<?php
/**
 * Modelo Contrato: contratos de alquiler que vinculan una propiedad,
 * un inquilino y un propietario (dueño), con los términos del alquiler.
 */

class Contrato
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, p.titulo AS property_titulo, p.agente_id,
                    i.nombre AS inquilino_nombre, d.nombre AS propietario_nombre
             FROM contratos c
             JOIN properties p ON p.id = c.property_id
             JOIN users i ON i.id = c.inquilino_id
             JOIN users d ON d.id = c.propietario_id
             WHERE c.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $contrato = $stmt->fetch();
        return $contrato ?: null;
    }

    /**
     * Lista de contratos según quién consulta.
     * $filters puede traer 'inquilino_id', 'propietario_id' o 'agente_id'.
     */
    public function list(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['inquilino_id'])) {
            $where[] = 'c.inquilino_id = :inquilino_id';
            $params['inquilino_id'] = $filters['inquilino_id'];
        }
        if (!empty($filters['propietario_id'])) {
            $where[] = 'c.propietario_id = :propietario_id';
            $params['propietario_id'] = $filters['propietario_id'];
        }
        if (!empty($filters['agente_id'])) {
            $where[] = 'p.agente_id = :agente_id';
            $params['agente_id'] = $filters['agente_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare(
            "SELECT c.*, p.titulo AS property_titulo,
                    i.nombre AS inquilino_nombre, d.nombre AS propietario_nombre
             FROM contratos c
             JOIN properties p ON p.id = c.property_id
             JOIN users i ON i.id = c.inquilino_id
             JOIN users d ON d.id = c.propietario_id
             $whereSql
             ORDER BY c.created_at DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO contratos (property_id, inquilino_id, propietario_id, monto_inicial, fecha_inicio, duracion_meses, frecuencia_ajuste_meses)
             VALUES (:property_id, :inquilino_id, :propietario_id, :monto_inicial, :fecha_inicio, :duracion_meses, :frecuencia_ajuste_meses)'
        );
        $stmt->execute([
            'property_id'             => $data['property_id'],
            'inquilino_id'            => $data['inquilino_id'],
            'propietario_id'          => $data['propietario_id'],
            'monto_inicial'           => $data['monto_inicial'],
            'fecha_inicio'            => $data['fecha_inicio'],
            'duracion_meses'          => $data['duracion_meses'],
            'frecuencia_ajuste_meses' => $data['frecuencia_ajuste_meses'],
        ]);
        return (int) $this->db->lastInsertId();
    }
}
