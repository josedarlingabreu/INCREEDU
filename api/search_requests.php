<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$q = trim(strip_tags($_GET['q'] ?? ''));
$termino = '%' . $q . '%';

try {
    $stmt = $pdo->prepare('SELECT id, nombre, matricula, carrera, email, tipo, estado, descripcion, fecha, created_at FROM solicitudes WHERE nombre LIKE :q1 OR matricula LIKE :q2 OR carrera LIKE :q3 OR tipo LIKE :q4 ORDER BY id DESC');
    $stmt->execute([':q1' => $termino, ':q2' => $termino, ':q3' => $termino, ':q4' => $termino]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al buscar solicitudes: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
