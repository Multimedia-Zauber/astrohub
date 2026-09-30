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
        if (!is_dir($directory)) mkdir($directory, 0775, true);
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
        $pdo->exec('CREATE TABLE IF NOT EXISTS system_state (key TEXT PRIMARY KEY, value TEXT NOT NULL, updated_at TEXT NOT NULL)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS profiles (id TEXT PRIMARY KEY, name TEXT NOT NULL, language TEXT NOT NULL DEFAULT "de", experience_level TEXT NOT NULL DEFAULT "simple", created_at TEXT NOT NULL, updated_at TEXT NOT NULL)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS interests (profile_id TEXT NOT NULL, interest TEXT NOT NULL, PRIMARY KEY(profile_id,interest), FOREIGN KEY(profile_id) REFERENCES profiles(id) ON DELETE CASCADE)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS workspaces (id TEXT PRIMARY KEY, profile_id TEXT NOT NULL, name TEXT NOT NULL, type TEXT NOT NULL, icon TEXT NOT NULL DEFAULT "🔭", is_default INTEGER NOT NULL DEFAULT 0, created_at TEXT NOT NULL, FOREIGN KEY(profile_id) REFERENCES profiles(id) ON DELETE CASCADE)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS locations (id TEXT PRIMARY KEY, profile_id TEXT NOT NULL, name TEXT NOT NULL, latitude REAL NOT NULL, longitude REAL NOT NULL, elevation_m REAL, timezone TEXT, is_default INTEGER NOT NULL DEFAULT 0, privacy TEXT NOT NULL DEFAULT "private", created_at TEXT NOT NULL, FOREIGN KEY(profile_id) REFERENCES profiles(id) ON DELETE CASCADE)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS preference_signals (id INTEGER PRIMARY KEY AUTOINCREMENT, profile_id TEXT NOT NULL, signal_type TEXT NOT NULL, subject TEXT NOT NULL, weight REAL NOT NULL DEFAULT 1.0, created_at TEXT NOT NULL, FOREIGN KEY(profile_id) REFERENCES profiles(id) ON DELETE CASCADE)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS meteor_sessions (id TEXT PRIMARY KEY, profile_id TEXT NOT NULL DEFAULT "local", location_id TEXT, name TEXT NOT NULL, started_at TEXT NOT NULL, ended_at TEXT, notes TEXT, created_at TEXT NOT NULL)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS meteor_events (id TEXT PRIMARY KEY, session_id TEXT NOT NULL, observed_at TEXT NOT NULL, observed_at_ms INTEGER NOT NULL, input_method TEXT NOT NULL DEFAULT "touch", magnitude REAL, shower TEXT, notes TEXT, created_at TEXT NOT NULL, FOREIGN KEY(session_id) REFERENCES meteor_sessions(id) ON DELETE CASCADE)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_meteor_events_session_time ON meteor_events(session_id, observed_at_ms)');
        $statement=$pdo->prepare('INSERT INTO system_state (key,value,updated_at) VALUES (:key,:value,:updated_at) ON CONFLICT(key) DO UPDATE SET value=excluded.value,updated_at=excluded.updated_at');
        $statement->execute([':key'=>'database_status',':value'=>'ready',':updated_at'=>gmdate(DATE_ATOM)]);
    }
}
