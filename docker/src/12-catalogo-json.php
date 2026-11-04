<?php

require_once __DIR__ . '/lib/canciones.php';
require __DIR__ . '/lib/almacen-json.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo musical desde JSON</title>
</head>
<body>
  <h1>Catálogo musical desde JSON</h1>
  <?php if (empty($songs)): ?>
    <p>Todavía no hay canciones</p>
  <?php else: ?>
    <?php foreach ($songs as $song): ?>
        <section>
            <h2><?= htmlspecialchars($song['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
            <p>Artista: <?= htmlspecialchars($song['artist'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p>Categoría: <?= htmlspecialchars($song['category'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
            <p>Duración: <?= $song['duration'] ?> segundos</p>
        </section>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>