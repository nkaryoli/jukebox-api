<?php

require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';

function responder_json(int $codigo, array $datos): never
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
    );

    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    responder_json(401, [
        'ok' => false,
        'error' => 'No autorizado. Se requiere iniciar sesión.'
    ]);
}

try {
    $songs = leer_json_bloqueado('/var/www/data/canciones.json');

    responder_json(200, [
        'ok' => true,
        'data' => $songs
    ]);
} catch (Throwable $e) {
    responder_json(500, [
        'ok' => false,
        'error' => 'Error al leer el catálogo musical.'
    ]);
}

?>