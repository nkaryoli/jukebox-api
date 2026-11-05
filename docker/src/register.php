<?php
require_once __DIR__ . '/lib/sesion.php';

$errores = [];
$alias = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$recibido = $_POST['csrf'] ?? null;

	if ( !is_string($recibido) || !hash_equals($_SESSION['csrf'], $recibido) ) {
		http_response_code(403);
		exit('Error 403: token CSRF no válido.');
	}

	$aliasRecibido = $_POST['alias'] ?? null;
	$emailRecibido = $_POST['email'] ?? null;
	$passwordRecibido = $_POST['password'] ?? null;
	$confirmacionRecibida = $_POST['confirmacion'] ?? null;

	if (
		!is_string($aliasRecibido) || !is_string($emailRecibido) ||
		!is_string($passwordRecibido) || !is_string($confirmacionRecibida)
	) {
		$errores[] = 'Todos los campos deben contener texto.';
	} else {
		$alias = trim($aliasRecibido);
		$email = strtolower(trim($emailRecibido));
		$password = $passwordRecibido;
		$confirmacion = $confirmacionRecibida;

		if (mb_strlen($alias, 'UTF-8') < 2 || mb_strlen($alias, 'UTF-8') > 30) {
			$errores[] = 'El alias debe tener entre 2 y 30 caracteres.';
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errores[] = 'El correo electrónico no tiene un formato válido.';
		}

		if (mb_strlen($password, 'UTF-8') < 12 || mb_strlen($password, 'UTF-8') > 64) {
			$errores[] = 'La contraseña debe tener entre 12 y 64 caracteres.';
		}

		if (strlen($password) > 72) {
			$errores[] = 'La contraseña no puede superar los 72 bytes.';
		}

		if ($password !== $confirmacion) {
			$errores[] = 'Las contraseñas no coinciden.';
		}

		if (empty($errores)) {
			$hash = password_hash($password, PASSWORD_DEFAULT);
			$user = [
				'alias' => $alias,
				'email' => $email,
				'password_hash' => $hash
			];
		}
	}
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Crear cuenta</title>
</head>
<body>
	<h1>Registrar una cuenta</h1>
	<?php foreach ($errores as $error): ?>
		<p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endforeach; ?>
	<form method="post">
		<input
			type="hidden"
			name="csrf"
			value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>"
		>
		<label for="alias">Alias:</label>
		<input
			type="text"
			id="alias"
			name="alias"
			value="<?= htmlspecialchars($alias, ENT_QUOTES, 'UTF-8') ?>"
		>
		<br/>
		<label for="email">Correo electrónico:</label>
		<input
			type="email"
			id="email"
			name="email"
			value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
		>
		<br/>
		<label for="password">Contraseña:</label>
		<input
			type="password"
			id="password"
			name="password"
		>
		<br/>
		<label for="confirmacion">Repetir contraseña:</label>
		<input
			type="password"
			id="confirmacion"
			name="confirmacion"
		>
		<br/>
		<br/>
		<button type="submit">Registrar</button>
	</form>
</body>
</html>