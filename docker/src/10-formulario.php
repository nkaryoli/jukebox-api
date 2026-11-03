<?php
require_once __DIR__ . '/lib/canciones.php';

$errors = [];

$title = '';
$artist = '';
$category = '';
$duration = '';
$src = '';
$preview = false;

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

  if (is_string($title) && trim($title) === '') {
    $errors['title'] = 'El título es obligatorio.';
  }

	if (trim($artist) === '') {
		$errors['artist'] = 'El artista es obligatorio.';
	}

	$allowedCategories = [ 'electronica', 'rock', 'bandas-sonoras' ];

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

	$allowedSources = [ 'audio/demo-01.mp3', 'audio/demo-02.mp3' ];

	if (!in_array($src, $allowedSources, true)) {
		$errors['src'] = 'Selecciona un audio válido.';
	}

	if (!empty($errors)) {
    	http_response_code(400);
	} else {
		$preview = true;
	}
}

$safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeArtist = htmlspecialchars($artist, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeCategory = htmlspecialchars($category, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeDuration = htmlspecialchars($duration, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$safeSrc = htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Formulario de canción</title>
</head>
<body>
    <h1>Preparar una canción</h1>
	
	<?php if ($preview): ?>
		<h2>Vista previa de la canción</h2>

		<p><strong>Título:</strong> <?= $safeTitle ?></p>
		<p><strong>Artista:</strong> <?= $safeArtist ?></p>
		<p><strong>Categoría:</strong> <?= $safeCategory ?></p>
		<p><strong>Duración:</strong> <?= duracionLegible($durationValue) ?></p>
		<p><strong>Audio:</strong> <?= $safeSrc ?></p>

		<p><strong>Vista previa, canción no guardada.</strong></p>
	<?php endif; ?>

    <form method="post">
		<label>
			Título:
			<input type="text" name="title" value="<?= $safeTitle ?>" >
		</label>
		<?php if (isset($errors['title'])): ?>
			<p><?= htmlspecialchars($errors['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
		<?php endif; ?>
		<br>
		<label>
			Artista:
			<input type="text" name="artist" value="<?= $safeArtist ?>" >
		</label>
		<?php if (isset($errors['artist'])): ?>
			<p><?= htmlspecialchars($errors['artist'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
		<?php endif; ?>
		<br>
		<label>
			Categoría:
			<select name="category">
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
		</label>
		<?php if (isset($errors['category'])): ?>
			<p><?= htmlspecialchars($errors['category'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
		<?php endif; ?>
		<br>
		<label>
			Duración en segundos:
			<input
				type="number"
				name="duration"
				value="<?= $safeDuration ?>"
			>
		</label>
		<?php if (isset($errors['duration'])): ?>
			<p>
				<?= htmlspecialchars($errors['duration'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
			</p>
		<?php endif; ?>
			<br>
		<label>
			Audio:
			<select name="src">
				<option value="audio/demo-01.mp3" <?= $src === 'audio/demo-01.mp3' ? 'selected' : '' ?>>
          Demo 01
        </option>
				<option value="audio/demo-02.mp3" <?= $src === 'audio/demo-02.mp3' ? 'selected' : '' ?>>
          Demo 02</option>
			</select>
		</label>
		<?php if (isset($errors['src'])): ?>
			<p><?= htmlspecialchars($errors['src'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
		<?php endif; ?>
		<br>
		<button type="submit">Enviar</button>
    </form>
</body>
</html>