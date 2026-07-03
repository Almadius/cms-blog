<?php

declare(strict_types=1);

namespace App\Database;

use App\Config;
use PDO;

/**
 * PDO factory — single connection per request lifecycle.
 */
final class Connection
{
    private static ?PDO $pdo = null;

    public static function get(Config $config): PDO
    {
        if (self::$pdo === null) {
            $db = $config->get('db');
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $db['host'],
                $db['port'],
                $db['name'],
            );

            self::$pdo = new PDO($dsn, $db['user'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$pdo;
    }

    /** Reset connection — useful in tests or CLI scripts. */
    public static function reset(): void
    {
        self::$pdo = null;
    }
}
