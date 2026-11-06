<?php

require_once __DIR__ . '/lib/acceso.php';
require_once __DIR__ . '/lib/canciones.php';
require_once __DIR__ . '/lib/almacen-json.php';

$songs = leer_json_bloqueado('/var/www/data/canciones.json');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Catálogo de canciones</title>
</head>
<body>
    <h1>Catálogo de canciones</h1>
    <p>
        <a href="home.php">Volver a la página principal</a>
    </p>
    <p>
        <a href="cancion-nueva.php">Añadir una canción</a>
    </p>
    <?php if (empty($songs)): ?>
        <p>Todavía no hay canciones.</p>
    <?php else: ?>
        <?php foreach ($songs as $song): ?>
            <section>
                <h2>
                    <?= htmlspecialchars((string)($song['title'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </h2>
                <p>
                    Artista:
                    <?= htmlspecialchars((string)($song['artist'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </p>
                <p>
                    Categoría:
                    <?= htmlspecialchars((string)($song['category'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                </p>
                <p>
                    Duración:
                    <?php
                    if (isset($song['duration']) && is_numeric($song['duration'])) {
                        echo htmlspecialchars(
                            duracionLegible((int)$song['duration']),
                            ENT_QUOTES | ENT_SUBSTITUTE,
                            'UTF-8'
                        );
                    } else {
                        echo 'No disponible';
                    }
                    ?>
                </p>
                <?php if (isset($song['id']) && is_numeric($song['id']) && (int)$song['id'] > 0): ?>
                    <p>
                        <a href="cancion-editar.php?id=<?= (int)$song['id'] ?>">
                            Editar canción
                        </a>
                    </p>
                    <form method="POST">
                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"
                        >
                        <input
                            type="hidden"
                            name="id"
                            value="<?= (int)$song['id'] ?>"
                        >
                        <button type="submit" disabled>
                            Eliminar canción
                        </button>
                    </form>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>