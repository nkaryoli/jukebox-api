<?php

require_once __DIR__ . '/lib/acceso.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Inicio</title>
</head>
<body>
	<h1>Bienvenido, <?= htmlspecialchars($_SESSION['alias'], ENT_QUOTES, 'UTF-8') ?></h1>
	<p>Has iniciado sesión correctamente.</p>
	<form method="post" action="logout.php">
		<input
			type="hidden"
			name="csrf"
			value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>"
		>
		<button type="submit">Cerrar sesión</button>
	</form>
</body>
</html>