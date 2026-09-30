<?php

declare(strict_types=1);

namespace AstroHub\Core;

use PDO;

final class Database
{
    public static function connect(?string $path = null): PDO
    {
        $path ??= dirname(__DIR__, 2) . '/runtime/astrohub.sqlite';
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');

        self::migrate($pdo);

        return $pdo;
    }

    private static function migrate(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS system_state (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )'
        );

        $statement = $pdo->prepare(
            'INSERT INTO system_state (key, value, updated_at)
             VALUES (:key, :value, :updated_at)
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at'
        );
        $statement->execute([
            ':key' => 'database_status',
            ':value' => 'ready',
            ':updated_at' => gmdate(DATE_ATOM),
        ]);
    }
}
