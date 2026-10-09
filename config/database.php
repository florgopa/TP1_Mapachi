<?php
/**
 * Conexión a la base de datos usando PDO.
 * Compatible con XAMPP y entornos de producción.
 */

class Database
{
    private static ?PDO $instance = null;

    private const CHARSET = 'utf8mb4';

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Si no existen variables de entorno, utiliza la configuración local.
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'mapachi';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASSWORD');

            if ($pass === false) {
                $pass = '';
            }

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $host,
                $port,
                $dbname,
                self::CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $pass,
                    $options
                );
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());

                http_response_code(500);
                exit('No se pudo conectar con la base de datos.');
            }
        }

        return self::$instance;
    }
}