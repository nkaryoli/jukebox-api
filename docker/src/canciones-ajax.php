<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/acceso.php';

$audios = [];
foreach (glob(__DIR__ . '/audio/*.mp3') ?: [] as $archivo) {
	$audios[] = 'audio/' . basename($archivo);
}
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
	<form id="formulario-cancion">
		<h2 id="titulo-formulario">Añadir canción</h2>
		<p>
			<label for="title">Título</label>
			<input type="text" id="title" name="title" required maxlength="80">
		</p>
		<p>
			<label for="artist">Artista</label>
			<input type="text" id="artist" name="artist" required maxlength="80">
		</p>
		<p>
			<label for="category">Categoría</label>
			<select id="category" name="category" required>
				<option value="">Elige una categoría</option>
				<option value="electronica">Electrónica</option>
				<option value="rock">Rock</option>
				<option value="bandas-sonoras">Bandas sonoras</option>
			</select>
		</p>
		<p>
			<label for="duration">Duración (segundos)</label>
			<input type="number" id="duration" name="duration" required min="1" max="1800" step="1">
		</p>
		<p>
			<label for="src">Audio</label>
			<select id="src" name="src" required>
				<option value="">Elige un audio</option>
				<?php foreach ($audios as $audio): ?>
					<option value="<?= htmlspecialchars($audio, ENT_QUOTES, 'UTF-8') ?>">
						<?= htmlspecialchars(basename($audio), ENT_QUOTES, 'UTF-8') ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<button type="submit" id="boton-guardar">Añadir canción</button>
	</form>
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