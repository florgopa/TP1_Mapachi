<?php
/**
 * BcraService: obtiene el valor del ICL (Índice para Contratos de Locación)
 * desde la API pública y gratuita del BCRA (no requiere API key).
 *
 * Estrategia:
 *  1. Buscamos el ID de la variable "ICL" una sola vez, consultando el catálogo
 *     de variables monetarias, y lo guardamos en app_settings para no repetir
 *     esa búsqueda en cada request.
 *  2. Los valores de ICL por fecha se cachean en icl_cache, así solo se golpea
 *     la API una vez por fecha nueva.
 *  3. Si la API no responde (sin internet, caída, cambio de formato, etc.),
 *     devolvemos null y quien llama debe manejar el caso "no se pudo calcular"
 *     dejando la cuota en estado 'a_convenir' para carga manual.
 */

class BcraService
{
    private const BASE_URL = 'https://api.bcra.gob.ar/estadisticas/v3.0/monetarias';
    private const TIMEOUT_SEGUNDOS = 6;

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Devuelve el valor del ICL más cercano (hacia atrás) a la fecha pedida,
     * o null si no se pudo obtener (sin internet, API caída, fecha sin dato, etc).
     */
    public function getValorIcl(string $fecha): ?float
    {
        // 1. ¿Ya lo tenemos en caché?
        $cacheado = $this->getDesdeCache($fecha);
        if ($cacheado !== null) {
            return $cacheado;
        }

        // 2. Necesitamos el ID de variable del ICL
        $idVariable = $this->getIdVariableIcl();
        if ($idVariable === null) {
            return null;
        }

        // 3. Pedimos un rango de ~10 días hasta la fecha buscada (el ICL no
        // se publica todos los días del calendario, así que pedimos margen
        // y nos quedamos con el valor más reciente disponible dentro del rango).
        $hasta = $fecha;
        $desde = date('Y-m-d', strtotime($fecha . ' -12 days'));

        $data = $this->fetchJson(self::BASE_URL . "/{$idVariable}?desde={$desde}&hasta={$hasta}");
        if ($data === null || empty($data['results'])) {
            return null;
        }

        // La API devuelve los resultados con 'fecha' y 'valor'; nos quedamos
        // con el de fecha más reciente (el último del array, según orden ascendente típico).
        $registros = $data['results'];
        usort($registros, fn($a, $b) => strcmp($a['fecha'], $b['fecha']));
        $ultimo = end($registros);

        if (!isset($ultimo['valor'])) {
            return null;
        }

        $valor = (float) $ultimo['valor'];
        $this->guardarEnCache($fecha, $valor);

        return $valor;
    }

    /** Calcula el nuevo monto aplicando la variación del ICL entre dos fechas */
    public function calcularAjuste(float $montoBase, string $fechaBase, string $fechaActual): ?array
    {
        $iclBase = $this->getValorIcl($fechaBase);
        $iclActual = $this->getValorIcl($fechaActual);

        if ($iclBase === null || $iclActual === null || $iclBase <= 0) {
            return null;
        }

        $nuevoMonto = round($montoBase * ($iclActual / $iclBase), 2);

        return [
            'monto'      => $nuevoMonto,
            'icl_base'   => $iclBase,
            'icl_actual' => $iclActual,
        ];
    }

    // -----------------------------------------------------------------
    // Internos
    // -----------------------------------------------------------------

    private function getDesdeCache(string $fecha): ?float
    {
        $stmt = $this->db->prepare('SELECT valor FROM icl_cache WHERE fecha = :fecha');
        $stmt->execute(['fecha' => $fecha]);
        $valor = $stmt->fetchColumn();
        return $valor !== false ? (float) $valor : null;
    }

    private function guardarEnCache(string $fecha, float $valor): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO icl_cache (fecha, valor) VALUES (:fecha, :valor)
             ON DUPLICATE KEY UPDATE valor = :valor2'
        );
        $stmt->execute(['fecha' => $fecha, 'valor' => $valor, 'valor2' => $valor]);
    }

    private function getIdVariableIcl(): ?int
    {
        // ¿Ya lo tenemos guardado de una consulta anterior?
        $stmt = $this->db->prepare('SELECT valor FROM app_settings WHERE clave = :clave');
        $stmt->execute(['clave' => 'bcra_id_variable_icl']);
        $guardado = $stmt->fetchColumn();
        if ($guardado !== false) {
            return (int) $guardado;
        }

        // Si no, consultamos el catálogo completo de variables monetarias y
        // buscamos la que mencione "ICL" o "Contratos de Locación" en su descripción.
        $data = $this->fetchJson(self::BASE_URL);
        if ($data === null || empty($data['results'])) {
            return null;
        }

        foreach ($data['results'] as $variable) {
            $desc = mb_strtolower($variable['descripcion'] ?? '');
            if (str_contains($desc, 'icl') || str_contains($desc, 'contratos de locación') || str_contains($desc, 'contratos de locacion')) {
                $id = (int) $variable['idVariable'];
                $this->guardarSetting('bcra_id_variable_icl', (string) $id);
                return $id;
            }
        }

        return null;
    }

    private function guardarSetting(string $clave, string $valor): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO app_settings (clave, valor) VALUES (:clave, :valor)
             ON DUPLICATE KEY UPDATE valor = :valor2'
        );
        $stmt->execute(['clave' => $clave, 'valor' => $valor, 'valor2' => $valor]);
    }

    /** GET con timeout corto. Devuelve null ante CUALQUIER error (sin internet, timeout, JSON inválido, etc). */
    private function fetchJson(string $url): ?array
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT_SEGUNDOS,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT_SEGUNDOS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $error || $httpCode !== 200) {
            return null;
        }

        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }
}
