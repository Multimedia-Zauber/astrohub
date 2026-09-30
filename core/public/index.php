<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use AstroHub\Core\Database;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/v1/health') {
    try {
        $pdo = Database::connect();
        $database = $pdo->query("SELECT value FROM system_state WHERE key = 'database_status'")->fetchColumn();

        $scienceUrl = getenv('ASTROHUB_SCIENCE_URL') ?: 'http://127.0.0.1:8090/health';
        $context = stream_context_create(['http' => ['timeout' => 1.5, 'ignore_errors' => true]]);
        $scienceRaw = @file_get_contents($scienceUrl, false, $context);
        $science = $scienceRaw !== false ? json_decode($scienceRaw, true) : null;

        echo json_encode([
            'status' => 'ok',
            'service' => 'astrohub-core',
            'version' => '0.1.0-alpha',
            'database' => $database ?: 'unknown',
            'science' => $science['status'] ?? 'offline',
            'time_utc' => gmdate(DATE_ATOM),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    } catch (Throwable $error) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $error->getMessage()]);
        exit;
    }
}

http_response_code(404);
echo json_encode([
    'status' => 'not_found',
    'message' => 'AstroHub API endpoint not found.',
]);
