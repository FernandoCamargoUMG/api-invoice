<?php
declare(strict_types=1);

namespace InvoiceSystem\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = [
                'host'   => getenv('DB_HOST') ?: 'host.docker.internal',
                'dbname' => getenv('DB_NAME') ?: 'invoice_system',
                'user'   => getenv('DB_USER') ?: 'root',
                'pass'   => getenv('DB_PASS') ?: '',
            ];

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";

            try {
                self::$pdo = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException('Error de conexión: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
