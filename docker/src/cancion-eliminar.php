<?php
require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/almacen-json.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo !== 'GET' && $metodo !== 'POST') {
    header('Allow: GET, POST');
    http_response_code(405);
    exit('Método no permitido.');
}

$idValue = $metodo === 'POST' ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null);

if (!is_string($idValue) && !is_int($idValue)) {
    http_response_code(400);
    exit('El identificador no es válido.');
}

$id = filter_var($idValue, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('El identificador no es válido.');
}

$ruta = '/var/www/data/canciones.json';

if ($metodo === 'POST') {
    $csrf = $_POST['csrf'] ?? '';

    if (!is_string($csrf) ||!isset($_SESSION['csrf']) ||  !hash_equals($_SESSION['csrf'], $csrf)) {
        http_response_code(403);
        exit('El token de seguridad no es válido.');
    }

    try {
        actualizar_json_bloqueado(
            $ruta,
            function (array $songs) use ($id): array {
                foreach ($songs as $position => $song) {
                    if (
                        isset($song['id']) &&
                        $song['id'] === $id
                    ) {
                        unset($songs[$position]);
                        return array_values($songs);
                    }
                }
                throw new DomainException(
                    'No se ha encontrado la canción.'
                );
            }
        );
    } catch (DomainException $e) {
        http_response_code(404);
        exit('No se ha encontrado la canción.');
    }
    header('Location: canciones.php', true, 303);
    exit;
}

$songs = leer_json_bloqueado($ruta);

$song = null;

foreach ($songs as $item) {
    if (
        isset($item['id']) &&
        $item['id'] === $id
    ) {
        $song = $item;
        break;
    }
}

if ($song === null) {
    http_response_code(404);
    exit('No se ha encontrado la canción.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Eliminar una canción</title>
</head>
<body>
    <h1>Confirmar eliminación</h1>
    <p>
        ¿Estás seguro de que quieres eliminar la canción
        <strong>
            <?= htmlspecialchars($song['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
        </strong>?
    </p>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
        <button type="submit">Confirmar eliminación</button>
    </form>
    <p><a href="canciones.php">Cancelar</a></p>
</body>
</html>