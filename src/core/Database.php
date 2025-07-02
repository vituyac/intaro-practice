<?php
namespace App\core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if (self::$pdo === null) {
            $config = parse_ini_file(__DIR__ . '/../../.env');
            try {
                $dsn = "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}";
                self::$pdo = new PDO($dsn,
                    $config['DB_USER'],
                    $config['DB_PASS'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
                );
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
