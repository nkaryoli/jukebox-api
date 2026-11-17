<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/acceso.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
	<title>Gestión AJAX del catálogo</title>
	<script src="scripts/catalogo-ajax.js" defer></script>
</head>
<body>
	<h1>Gestión AJAX del catálogo</h1>
	<p>
			<a href="home.php">Volver a la home</a> ·
			<a href="canciones.php">Gestión clásica</a>
	</p>
	<p id="mensaje" role="status" aria-live="polite"></p>
	<table>
			<caption>Canciones del catálogo</caption>
			<thead>
					<tr>
							<th scope="col">Título</th>
							<th scope="col">Artista</th>
							<th scope="col">Categoría</th>
							<th scope="col">Duración</th>
							<th scope="col">Acciones</th>
					</tr>
			</thead>
			<tbody id="tabla-canciones"></tbody>
	</table>
</body>
</html>