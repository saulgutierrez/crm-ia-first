<?php

declare(strict_types=1);

namespace App\Repositories;

class Database
{
    private static ?\PDO $instance = null;

    /**
     * Connect to the database using the given config, or return existing connection.
     */
    public static function connect(array $config): \PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            self::$instance = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options'] ?? [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$instance;
    }

    /**
     * Get the current PDO instance.
     */
    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Database not connected. Call Database::connect() first.');
        }

        return self::$instance;
    }

    /**
     * Disconnect and reset the instance.
     */
    public static function disconnect(): void
    {
        self::$instance = null;
    }

    /**
     * Begin a transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Roll back the current transaction.
     */
    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }
}
