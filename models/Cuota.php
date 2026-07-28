<?php
/**
 * Modelo Cuota: las cuotas mensuales de un contrato de alquiler.
 * Se generan todas juntas al crear el contrato.
 */

class Cuota
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Genera las N cuotas de un contrato recién creado */
    public function generarParaContrato(int $contratoId, string $fechaInicio, int $duracionMeses, int $frecuenciaAjuste, float $montoInicial): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cuotas (contrato_id, numero_cuota, mes_vencimiento, monto, estado)
             VALUES (:contrato_id, :numero, :mes, :monto, :estado)'
        );

        $fecha = new DateTime($fechaInicio);

        for ($i = 1; $i <= $duracionMeses; $i++) {
            $bloque = intdiv($i - 1, $frecuenciaAjuste);

            if ($bloque === 0) {
                // Primer período: monto fijo, el que se pactó al firmar
                $monto = $montoInicial;
                $estado = 'pendiente';
            } else {
                // Períodos siguientes: a definir por ajuste ICL (o carga manual)
                $monto = null;
                $estado = 'a_convenir';
            }

            $stmt->execute([
                'contrato_id' => $contratoId,
                'numero'      => $i,
                'mes'         => $fecha->format('Y-m-d'),
                'monto'       => $monto,
                'estado'      => $estado,
            ]);

            $fecha->modify('+1 month');
        }
    }

    public function listByContrato(int $contratoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM cuotas WHERE contrato_id = :contrato_id ORDER BY numero_cuota ASC'
        );
        $stmt->execute(['contrato_id' => $contratoId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT q.*, c.inquilino_id, c.propietario_id, c.property_id, c.frecuencia_ajuste_meses,
                    p.agente_id, p.titulo AS property_titulo
             FROM cuotas q
             JOIN contratos c ON c.id = q.contrato_id
             JOIN properties p ON p.id = c.property_id
             WHERE q.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $cuota = $stmt->fetch();
        return $cuota ?: null;
    }

    /** El inquilino sube el comprobante de esa cuota */
    public function subirComprobante(int $id, string $archivo): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cuotas SET archivo = :archivo, estado = 'pagado', fecha_pago = CURDATE() WHERE id = :id"
        );
        $stmt->execute(['archivo' => $archivo, 'id' => $id]);
    }

    /** Admin/agente marca manualmente el estado (ej: confirma el pago, o revierte) */
    public function actualizarEstado(int $id, string $estado): void
    {
        $fechaPago = $estado === 'pagado' ? 'CURDATE()' : 'NULL';
        $stmt = $this->db->prepare(
            "UPDATE cuotas SET estado = :estado, fecha_pago = $fechaPago WHERE id = :id"
        );
        $stmt->execute(['estado' => $estado, 'id' => $id]);
    }

    /** Carga manual del monto de una cuota "a convenir" */
    public function cargarMontoManual(int $id, float $monto): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cuotas SET monto = :monto, estado = 'pendiente' WHERE id = :id"
        );
        $stmt->execute(['monto' => $monto, 'id' => $id]);
    }

    /** Aplica el resultado del cálculo de ICL a una cuota "a convenir" */
    public function aplicarAjusteIcl(int $id, float $monto, float $iclBase, float $iclActual): void
    {
        $stmt = $this->db->prepare(
            "UPDATE cuotas SET monto = :monto, icl_base = :icl_base, icl_actual = :icl_actual, estado = 'pendiente' WHERE id = :id"
        );
        $stmt->execute(['monto' => $monto, 'icl_base' => $iclBase, 'icl_actual' => $iclActual, 'id' => $id]);
    }

    /** Todas las cuotas "a_convenir" de un contrato cuyo mes ya llegó (listas para intentar ajustar) */
    public function pendientesDeAjuste(int $contratoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cuotas
             WHERE contrato_id = :contrato_id AND estado = 'a_convenir' AND mes_vencimiento <= CURDATE()
             ORDER BY numero_cuota ASC"
        );
        $stmt->execute(['contrato_id' => $contratoId]);
        return $stmt->fetchAll();
    }

    /** Última cuota con monto definido antes de la indicada (para tomar como base del ajuste) */
    public function ultimoMontoDefinido(int $contratoId, int $antesDeNumero): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM cuotas
             WHERE contrato_id = :contrato_id AND numero_cuota < :numero AND monto IS NOT NULL
             ORDER BY numero_cuota DESC LIMIT 1"
        );
        $stmt->execute(['contrato_id' => $contratoId, 'numero' => $antesDeNumero]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
