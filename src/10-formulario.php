<?php

require_once __DIR__ . '/lib/canciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

	$title = $_POST['title'] ?? '';
	$artist = $_POST['artist'] ?? '';
	$category = $_POST['category'] ?? '';
	$duration = $_POST['duration'] ?? '';
	$src = $_POST['src'] ?? '';

	if (!is_string($title)) {
		$errors['title'] = 'El título no es válido.';
		$title = '';
	}

	if (!is_string($artist)) {
		$errors['artist'] = 'El artista no es válido.';
		$artist = '';
	}

	if (!is_string($category)) {
		$errors['category'] = 'La categoría no es válida.';
		$category = '';
	}

	if (!is_string($duration)) {
		$errors['duration'] = 'La duración no es válida.';
		$duration = '';
	}

	if (!is_string($src)) {
		$errors['src'] = 'El audio no es válido.';
		$src = '';
	}

	if (trim($title) === '') {
		$errors['title'] = 'El título es obligatorio.';
	}

	if (trim($artist) === '') {
		$errors['artist'] = 'El artista es obligatorio.';
	}

	$allowedCategories = [
    'electronica',
    'rock',
    'bandas-sonoras'
	];

	if (!in_array($category, $allowedCategories, true)) {
		$errors['category'] = 'Selecciona una categoría válida.';
	}

	$durationValue = filter_var($duration, FILTER_VALIDATE_INT);

	if ($duration === '') {
		$errors['duration'] = 'La duración es obligatoria.';
	} elseif ($durationValue === false) {
		$errors['duration'] = 'La duración debe ser un número entero.';
	} elseif ($durationValue < 1 || $durationValue > 1800) {
		$errors['duration'] = 'La duración debe estar entre 1 y 1800 segundos.';
	}

	$allowedSources = [
    'audio/demo-01.mp3',
    'audio/demo-02.mp3'
	];

	if (!in_array($src, $allowedSources, true)) {
		$errors['src'] = 'Selecciona un audio válido.';
	}

	if (!empty($errors)) {
    http_response_code(400);
	}
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Formulario de canción</title>
</head>
<body>
    <h1>Preparar una canción</h1>
    <form method="post">
			<label>
					Título:
					<input type="text" name="title">
			</label>
			<br>
			<label>
					Artista:
					<input type="text" name="artist">
			</label>
			<br>
			<label>
					Categoría:
					<select name="category">
							<option value="electronica">Electrónica</option>
							<option value="rock">Rock</option>
							<option value="bandas-sonoras">Bandas sonoras</option>
					</select>
			</label>
			<br>
			<label>
					Duración en segundos:
					<input type="number" name="duration">
			</label>
			<br>
			<label>
					Audio:
					<select name="src">
							<option value="audio/demo-01.mp3">Demo 01</option>
							<option value="audio/demo-02.mp3">Demo 02</option>
					</select>
			</label>
			<br>
			<button type="submit">Enviar</button>
    </form>
</body>
</html>