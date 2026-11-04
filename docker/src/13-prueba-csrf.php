<?php

require_once __DIR__ . '/lib/sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$recibido = $_POST['csrf'] ?? null;

	if (
		!is_string($recibido) ||
		!hash_equals($_SESSION['csrf'], $recibido)
	) {
		http_response_code(403);
		exit('Error 403: token CSRF no válido.');
	}

	$mensaje = 'Token CSRF correcto. Envío aceptado.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Prueba CSRF</title>
</head>
<body>
	<h1>Prueba de protección CSRF</h1>

	<?php if (isset($mensaje)): ?>
		<p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endif; ?>

	<form method="post">
		<input
			type="hidden"
			name="csrf"
			value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>"
		>
		<button type="submit">Enviar formulario</button>
	</form>
</body>
</html>