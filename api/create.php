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

function limpiar($valor): string {
    return htmlspecialchars(strip_tags(trim((string) $valor)), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$nombre = limpiar($input['nombre'] ?? '');
$matricula = limpiar($input['matricula'] ?? '');
$carrera = limpiar($input['carrera'] ?? '');
$email = limpiar($input['email'] ?? '');
$tipo = limpiar($input['tipo'] ?? '');
$estado = limpiar($input['estado'] ?? 'pendiente');
$descripcion = limpiar($input['descripcion'] ?? '');

if ($nombre === '' || $matricula === '' || $email === '' || $tipo === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nombre, matrícula, email y tipo son obligatorios'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($carrera === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'La carrera es obligatoria'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'El email no es válido'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($estado, ['pendiente', 'aprobada', 'rechazada'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'El estado no es válido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO solicitudes (nombre, matricula, carrera, email, tipo, estado, descripcion) VALUES (:nombre, :matricula, :carrera, :email, :tipo, :estado, :descripcion)');
    $stmt->execute([
        ':nombre' => $nombre,
        ':matricula' => $matricula,
        ':carrera' => $carrera,
        ':email' => $email,
        ':tipo' => $tipo,
        ':estado' => $estado,
        ':descripcion' => $descripcion
    ]);

    echo json_encode([
        'success' => true,
        'id' => (int) $pdo->lastInsertId(),
        'message' => 'Solicitud creada'
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al crear la solicitud: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
