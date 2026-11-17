<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/sesion.php';
require_once __DIR__ . '/../lib/almacen-json.php';

const RUTA_CANCIONES = '/var/www/data/canciones.json';
const ACCIONES = ['list', 'create', 'update', 'delete'];
const CATEGORIAS = ['electronica', 'rock', 'bandas-sonoras'];
const MAX_TEXTO = 80;

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

function rutasAudioPermitidas(): array {
    $rutas = [];
    foreach (glob(__DIR__ . '/../audio/*.mp3') ?: [] as $archivo) {
        $rutas[] = 'audio/' . basename($archivo);
    }
    return $rutas;
}

function errorValidacion(string $mensaje): never {
    responderJson(422, ['ok' => false, 'error' => $mensaje]);
}

function validarCancion(array $datos): array {
    $limpio = [];

    foreach (['title' => 'título', 'artist' => 'artista'] as $campo => $nombre) {
        $valor = $datos[$campo] ?? null;
        if (!is_string($valor)) {
            errorValidacion("El campo $nombre debe ser texto.");
        }
        $valor = trim($valor);
        if ($valor === '') {
            errorValidacion("El $nombre es obligatorio.");
        }
        if (mb_strlen($valor) > MAX_TEXTO) {
            errorValidacion("El $nombre no puede superar los " . MAX_TEXTO . ' caracteres.');
        }
        $limpio[$campo] = $valor;
    }

    $categoria = $datos['category'] ?? null;
    if (!is_string($categoria) || !in_array($categoria, CATEGORIAS, true)) {
        errorValidacion('La categoría no es válida.');
    }
    $limpio['category'] = $categoria;

    $duracion = $datos['duration'] ?? null;
    if (!is_int($duracion) || $duracion < 1 || $duracion > 1800) {
        errorValidacion('La duración debe ser un entero entre 1 y 1800 segundos.');
    }
    $limpio['duration'] = $duracion;

    $audio = $datos['src'] ?? null;
    if (!is_string($audio) || !in_array($audio, rutasAudioPermitidas(), true)) {
        errorValidacion('El audio seleccionado no está autorizado.');
    }
    $limpio['src'] = $audio;

    return $limpio;
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

responderJson(400, ['ok' => false, 'error' => 'Acción todavía no disponible.']);