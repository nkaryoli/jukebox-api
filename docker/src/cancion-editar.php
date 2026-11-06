<?php

require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/canciones.php';
require_once __DIR__ . '/lib/almacen-json.php';

$errors = [];

$title = '';
$artist = '';
$category = '';
$duration = '';
$src = '';

$allowedCategories = [
    'electronica',
    'rock',
    'bandas-sonoras'
];

$allowedSources = [];

for ($i = 1; $i <= 12; $i++) {
    $allowedSources[] = 'audio/song' . $i . '.mp3';
}

$idValue = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['id'] ?? null)
    : ($_GET['id'] ?? null);

$id = filter_var($idValue, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    exit('El identificador no es válido.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf = $_POST['csrf'] ?? '';

    if (
        !is_string($csrf) ||
        !isset($_SESSION['csrf']) ||
        !hash_equals($_SESSION['csrf'], $csrf)
    ) {
        http_response_code(403);
        $errors['csrf'] = 'El token de seguridad no es válido.';
    } else {

        $resultado = validarDatosCancion(
            $_POST,
            $allowedCategories,
            $allowedSources
        );

        $datos = $resultado['data'];
        $errors = $resultado['errors'];

        $title = $datos['title'];
        $artist = $datos['artist'];
        $category = $datos['category'];
        $duration = $_POST['duration'] ?? '';
        $src = $datos['src'];

        if (!is_string($duration)) {
            $duration = '';
        }

        if (empty($errors)) {

            $found = false;

            actualizar_json_bloqueado(
                '/var/www/data/canciones.json',
                function (array $songs) use ($id, $datos, &$found): array {

                    foreach ($songs as $position => $song) {
                        if (
                            isset($song['id']) &&
                            $song['id'] === $id
                        ) {
                            $songs[$position] = [
                                'id' => $id,
                                'title' => $datos['title'],
                                'artist' => $datos['artist'],
                                'category' => $datos['category'],
                                'duration' => $datos['duration'],
                                'src' => $datos['src']
                            ];

                            $found = true;
                            break;
                        }
                    }

                    return $songs;
                }
            );

            if (!$found) {
                http_response_code(404);
                exit('No se ha encontrado la canción.');
            }

            header('Location: canciones.php', true, 303);
            exit;
        } else {
            http_response_code(400);
        }
    }

} else {

    $songs = leer_json_bloqueado('/var/www/data/canciones.json');

    $song = null;

    foreach ($songs as $position => $item) {
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

    $title = $song['title'];
    $artist = $song['artist'];
    $category = $song['category'];
    $duration = (string) $song['duration'];
    $src = $song['src'];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Editar una canción</title>
</head>
<body>
    <h1>Editar una canción</h1>
    <p>
        <a href="canciones.php">Volver al catálogo</a>
    </p>
    <?php if (!empty($errors)): ?>
        <div>
            <h2>Se han encontrado errores</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
        >
        <p>
            <label for="title">Título:</label>
            <input
                type="text"
                id="title"
                name="title"
                maxlength="80"
                required
                value="<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            >
        </p>
        <p>
            <label for="artist">Artista:</label>
            <input
                type="text"
                id="artist"
                name="artist"
                maxlength="80"
                required
                value="<?= htmlspecialchars($artist, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            >
        </p>
        <p>
            <label for="category">Categoría:</label>
            <select id="category" name="category" required>
                <option value="">Selecciona una categoría</option>
                <option value="electronica" <?= $category === 'electronica' ? 'selected' : '' ?>>
                    Electrónica
                </option>
                <option value="rock" <?= $category === 'rock' ? 'selected' : '' ?>>
                    Rock
                </option>
                <option value="bandas-sonoras" <?= $category === 'bandas-sonoras' ? 'selected' : '' ?>>
                    Bandas sonoras
                </option>
            </select>
        </p>
        <p>
            <label for="duration">Duración en segundos:</label>
            <input
                type="number"
                id="duration"
                name="duration"
                min="1"
                max="1800"
                step="1"
                required
                value="<?= htmlspecialchars($duration, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
            >
        </p>
        <p>
            <label for="src">Audio:</label>
            <select id="src" name="src" required>
                <option value="">Selecciona un audio</option>

                <?php foreach ($allowedSources as $audio): ?>
                    <option
                        value="<?= htmlspecialchars($audio, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                        <?= $src === $audio ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars(basename($audio), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
