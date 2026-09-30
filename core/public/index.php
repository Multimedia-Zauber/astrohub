<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use AstroHub\Core\Database;
use AstroHub\Core\MyAstroHub;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') { http_response_code(204); exit; }

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$body = json_decode(file_get_contents('php://input') ?: '{}', true) ?: [];

try {
    $pdo = Database::connect();
    $my = new MyAstroHub($pdo);

    if ($path === '/api/v1/health' && $method === 'GET') {
        $database = $pdo->query("SELECT value FROM system_state WHERE key='database_status'")->fetchColumn();
        $scienceUrl = getenv('ASTROHUB_SCIENCE_URL') ?: 'http://127.0.0.1:8090/health';
        $context = stream_context_create(['http'=>['timeout'=>1.5,'ignore_errors'=>true]]);
        $scienceRaw = @file_get_contents($scienceUrl, false, $context);
        $science = $scienceRaw !== false ? json_decode($scienceRaw, true) : null;
        echo json_encode(['status'=>'ok','service'=>'astrohub-core','version'=>'0.1.0-alpha','database'=>$database ?: 'unknown','science'=>$science['status'] ?? 'offline','time_utc'=>gmdate(DATE_ATOM)], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES); exit;
    }

    if ($path === '/api/v1/my-astrohub' && $method === 'GET') { echo json_encode($my->overview($_GET['profile'] ?? 'local')); exit; }
    if ($path === '/api/v1/my-astrohub/profile' && $method === 'POST') { echo json_encode($my->bootstrapProfile($body)); exit; }
    if ($path === '/api/v1/my-astrohub/workspaces' && $method === 'POST') { echo json_encode($my->addWorkspace((string)($body['profile_id'] ?? 'local'), $body)); exit; }
    if ($path === '/api/v1/my-astrohub/locations' && $method === 'POST') { echo json_encode($my->addLocation((string)($body['profile_id'] ?? 'local'), $body)); exit; }
    if ($path === '/api/v1/my-astrohub/signals' && $method === 'POST') { $my->addSignal((string)($body['profile_id'] ?? 'local'), $body); http_response_code(204); exit; }

    http_response_code(404); echo json_encode(['status'=>'not_found','message'=>'AstroHub API endpoint not found.']);
} catch (Throwable $error) {
    http_response_code(500); echo json_encode(['status'=>'error','message'=>$error->getMessage()]);
}
