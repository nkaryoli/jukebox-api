<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';
require_once __DIR__ . '/../lib/canciones.php';

const RUTA_CANCIONES = '/var/www/data/canciones.json';
const ACCIONES = ['list', 'create', 'update', 'delete'];

function responderJson(int $codigo, array $datos): never {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function exigirMetodo(string $metodo): void {
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        header('Allow: ' . $metodo);
        responderJson(405, ['ok' => false, 'error' => 'Método no permitido para esta acción.']);
    }
}

function comprobarToken(): void {
    $recibido = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $esperado = $_SESSION['csrf'] ?? null;

    if (!is_string($recibido) || !is_string($esperado) || !hash_equals($esperado, $recibido)) {
        responderJson(403, ['ok' => false, 'error' => 'Token de protección no válido.']);
    }
}

function leerCuerpoJson(): array {
    $tipo = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
    if ($tipo !== 'application/json') {
        responderJson(415, ['ok' => false, 'error' => 'El contenido debe enviarse como application/json.']);
    }

    $cuerpo = file_get_contents('php://input');
    if ($cuerpo === false) {
        responderJson(400, ['ok' => false, 'error' => 'No se pudo leer el cuerpo de la petición.']);
    }

    try {
        $objeto = json_decode($cuerpo, false, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $error) {
        responderJson(400, ['ok' => false, 'error' => 'El cuerpo no es un JSON válido.']);
    }

    if (!is_object($objeto)) {
        responderJson(400, ['ok' => false, 'error' => 'El cuerpo debe ser un objeto JSON.']);
    }

    return get_object_vars($objeto);
}

function errorValidacion(string $mensaje): never {
    responderJson(422, ['ok' => false, 'error' => $mensaje]);
}

class CancionNoEncontrada extends RuntimeException{}

function validarId(array $datos): int {
    $id = $datos['id'] ?? null;
    if (!is_int($id) || $id < 1) {
        errorValidacion('El id debe ser un entero positivo.');
    }
    return $id;
}

function validarCancion(array $datos): array {
    foreach (['title', 'artist', 'category', 'src'] as $campo) {
        if (!is_string($datos[$campo] ?? null)) {
            errorValidacion("El campo $campo debe ser texto.");
        }
    }

    if (!is_int($datos['duration'] ?? null)) {
        errorValidacion('La duración debe ser un número entero.');
    }

    $datos['duration'] = (string) $datos['duration'];

    $resultado = validarDatosCancion($datos, categoriasPermitidas(), audiosPermitidos());

    if ($resultado['errors'] !== []) {
        errorValidacion((string) reset($resultado['errors']));
    }

    return $resultado['data'];
}

if (!isset($_SESSION['usuario_id'])) {
    responderJson(401, ['ok' => false, 'error' => 'Debes iniciar sesión.']);
}

$accion = $_GET['action'] ?? 'list';
if (!is_string($accion) || !in_array($accion, ACCIONES, true)) {
    responderJson(400, ['ok' => false, 'error' => 'Acción desconocida.']);
}

if ($accion === 'list') {
    exigirMetodo('GET');

    try {
        $canciones = leer_json_bloqueado(RUTA_CANCIONES);
    } catch (Throwable $error) {
        error_log('catalogo.php list: ' . $error->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo acceder al catálogo.']);
    }

    responderJson(200, ['ok' => true, 'data' => array_values($canciones)]);
}

if ($accion === 'create') {
    exigirMetodo('POST');
    comprobarToken();
    $datos = validarCancion(leerCuerpoJson());

    try {
        $lista = actualizar_json_bloqueado(RUTA_CANCIONES, function (array $canciones) use ($datos): array {
            $maxId = 0;
            foreach ($canciones as $cancion) {
                if (isset($cancion['id']) && is_int($cancion['id']) && $cancion['id'] > $maxId) {
                    $maxId = $cancion['id'];
                }
            }
            $canciones[] = ['id' => $maxId + 1] + $datos;
            return array_values($canciones);
        });
    } catch (Throwable $error) {
        error_log('catalogo.php create: ' . $error->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo guardar el catálogo.']);
    }

    responderJson(201, ['ok' => true, 'data' => array_values($lista)]);
}

if ($accion === 'update') {
    exigirMetodo('POST');
    comprobarToken();
    $cuerpo = leerCuerpoJson();
    $id = validarId($cuerpo);
    $datos = validarCancion($cuerpo);

    try {
        $lista = actualizar_json_bloqueado(RUTA_CANCIONES, function (array $canciones) use ($id, $datos): array {
            foreach ($canciones as $posicion => $cancion) {
                if (isset($cancion['id']) && $cancion['id'] === $id) {
                    $canciones[$posicion] = ['id' => $id] + $datos;
                    return array_values($canciones);
                }
            }
            throw new CancionNoEncontrada('No existe la canción ' . $id);
        });
    } catch (CancionNoEncontrada $error) {
        responderJson(404, ['ok' => false, 'error' => 'La canción no existe.']);
    } catch (Throwable $error) {
        error_log('catalogo.php update: ' . $error->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo guardar el catálogo.']);
    }

    responderJson(200, ['ok' => true, 'data' => array_values($lista)]);
}

if ($accion === 'delete') {
    exigirMetodo('POST');
    comprobarToken();
    $id = validarId(leerCuerpoJson());

    try {
        $lista = actualizar_json_bloqueado(RUTA_CANCIONES, function (array $canciones) use ($id): array {
            foreach ($canciones as $posicion => $cancion) {
                if (isset($cancion['id']) && $cancion['id'] === $id) {
                    unset($canciones[$posicion]);
                    return array_values($canciones);
                }
            }
            throw new CancionNoEncontrada('No existe la canción ' . $id);
        });
    } catch (CancionNoEncontrada $error) {
        responderJson(404, ['ok' => false, 'error' => 'La canción no existe.']);
    } catch (Throwable $error) {
        error_log('catalogo.php delete: ' . $error->getMessage());
        responderJson(500, ['ok' => false, 'error' => 'No se pudo guardar el catálogo.']);
    }

    responderJson(200, ['ok' => true, 'data' => array_values($lista)]);
}

responderJson(400, ['ok' => false, 'error' => 'Acción todavía no disponible.']);