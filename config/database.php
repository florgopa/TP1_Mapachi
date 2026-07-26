<?php
/**
 * Conexión a la base de datos usando PDO.
 * Un solo punto de conexión, reutilizado en todos los modelos.
 */

class Database
{
    private static ?PDO $instance = null;

    // --- Ajustar estos datos según tu XAMPP/WAMP local ---
    private const HOST = '127.0.0.1';
    private const DBNAME = 'mapachi';
    private const USER = 'root';
    private const PASS = ''; // en XAMPP por defecto no tiene contraseña
    private const CHARSET = 'utf8mb4';

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME . ';charset=' . self::CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // consultas preparadas reales, evita inyección SQL
            ];

            try {
                self::$instance = new PDO($dsn, self::USER, self::PASS, $options);
            } catch (PDOException $e) {
                // No exponemos detalles de conexión en pantalla
                die('Error de conexión a la base de datos. Verificá que MySQL esté corriendo y que la base "mapachi" exista.');
            }
        }

        return self::$instance;
    }
}
