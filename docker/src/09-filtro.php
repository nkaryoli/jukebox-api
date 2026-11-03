<?php
require_once __DIR__ . '/lib/canciones.php';
$songs = require __DIR__ . '/lib/catalogo-inicial.php';

$category = $_GET['category'] ?? 'todas';

$allowed = ['todas', 'electronica', 'rock', 'bandas-sonoras', 'jazz'];

if (!is_string($category) || !in_array($category, $allowed, true)) {
    http_response_code(400);
    echo 'Categoría no válida';
    exit;
}

$filteredSongs = [];

foreach ($songs as $song) {
    if ($category === 'todas' || $song['category'] === $category) {
        $filteredSongs[] = $song;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Catálogo filtrado</title>
</head>
<body>
    <h1>Catálogo musical</h1>
    <p>
        Categoría seleccionada:
        <?= htmlspecialchars($category, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
    </p>
    <?php if (empty($filteredSongs)): ?>
        <p>No hay canciones en esta categoría.</p>
    <?php else: ?>
        <?php foreach ($filteredSongs as $song): ?>
            <article>
                <h2><?= htmlspecialchars($song['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
                <p>
                    Artista:
                    <?= htmlspecialchars($song['artist'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </p>
                <p>
                    Categoría:
                    <?= htmlspecialchars($song['category'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </p>
                <p>
									Duración: 
									<?= htmlspecialchars(duracionLegible($song['duration']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
								</p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
