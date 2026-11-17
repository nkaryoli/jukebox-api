<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';

const RUTA_CANCIONES = '/var/www/data/canciones.json';
const ACCIONES = ['list', 'create', 'update', 'delete'];

function responderJson(int $codigo, array $datos): void{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    responderJson(401, ['ok' => false, 'error' => 'Debes iniciar sesión.']);
}

$accion = $_GET['action'] ?? 'list';
if (!is_string($accion) || !in_array($accion, ACCIONES, true)) {
    responderJson(400, ['ok' => false, 'error' => 'Acción desconocida.']);
}

if ($accion === 'list') {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('Allow: GET');
        responderJson(405, ['ok' => false, 'error' => 'Método no permitido para esta acción.']);
    }
    try {
        $canciones = leer_json_bloqueado(RUTA_CANCIONES);
    } catch (Throwable $error) {
        error_log('catalogo.php list: ' . $error->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo acceder al catálogo.']);
    }

    responderJson(200, ['ok' => true, 'data' => array_values($canciones)]);
    if (!is_array($canciones)) {
        responderJson(500, ['ok' => false, 'error' => 'No se pudo acceder al catálogo.']);
    }
    responderJson(200, ['ok' => true, 'data' => array_values($canciones)]);
}
responderJson(400, ['ok' => false, 'error' => 'Acción todavía no disponible.']);