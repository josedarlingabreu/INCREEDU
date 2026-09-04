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

$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$nombre = limpiar($input['nombre'] ?? '');
$matricula = limpiar($input['matricula'] ?? '');
$carrera = limpiar($input['carrera'] ?? '');
$email = limpiar($input['email'] ?? '');
$tipo = limpiar($input['tipo'] ?? '');
$estado = limpiar($input['estado'] ?? 'pendiente');
$descripcion = limpiar($input['descripcion'] ?? '');

if (!$id) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'El id es obligatorio y debe ser válido'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($nombre === '' || $matricula === '' || $email === '' || $tipo === '' || $carrera === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Nombre, matrícula, carrera, email y tipo son obligatorios'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($estado, ['pendiente', 'aprobada', 'rechazada'], true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Email o estado no válido'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $exists = $pdo->prepare('SELECT id FROM solicitudes WHERE id = :id');
    $exists->execute([':id' => $id]);
    if (!$exists->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'La solicitud no existe'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE solicitudes SET nombre = :nombre, matricula = :matricula, carrera = :carrera, email = :email, tipo = :tipo, estado = :estado, descripcion = :descripcion WHERE id = :id');
    $stmt->execute([
        ':id' => $id,
        ':nombre' => $nombre,
        ':matricula' => $matricula,
        ':carrera' => $carrera,
        ':email' => $email,
        ':tipo' => $tipo,
        ':estado' => $estado,
        ':descripcion' => $descripcion
    ]);

    echo json_encode(['success' => true, 'message' => 'Solicitud actualizada'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la solicitud: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
