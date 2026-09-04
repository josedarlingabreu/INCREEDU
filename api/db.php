<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $config = [
        'host' => getenv('INCREEDU_DB_HOST') ?: 'localhost',
        'database' => getenv('INCREEDU_DB_NAME') ?: 'edurequest',
        'username' => getenv('INCREEDU_DB_USER') ?: 'root',
        'password' => getenv('INCREEDU_DB_PASSWORD') ?: ''
    ];
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión con la base de datos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
