<?php

header('Content-Type: application/json; charset=utf-8');

$resultado = [];
$fallo = false;

try {
	$dsn = 'pgsql:host=postgres;port=5432;dbname=jukebox_pg';
	$db = new PDO(
		$dsn,
		getenv('PGUSER'),
		getenv('PGPASSWORD'),
		[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
	);

	$name = $db->query('SELECT current_database()')->fetchColumn();

	$resultado['postgres'] = [
		'estado' => 'ok',
		'base' => $name
	];
} catch (PDOException $error) {
	error_log('Error de conexión PostgreSQL: ' . $error->getMessage());
	$resultado['postgres'] = [
		'estado' => 'error',
		'base' => null
	];
	$fallo = true;
}

try {
	$dsn = 'mysql:host=mariadb;port=3306;dbname=jukebox_maria;charset=utf8mb4';
	$db = new PDO(
		$dsn,
		getenv('MARIADB_USER'),
		getenv('MARIADB_PASSWORD'),
		[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
	);

	$name = $db->query('SELECT DATABASE()')->fetchColumn();

	$resultado['mariadb'] = [
		'estado' => 'ok',
		'base' => $name
	];
} catch (PDOException $error) {
	error_log('Error de conexión MariaDB: ' . $error->getMessage());
	$resultado['mariadb'] = [
		'estado' => 'error',
		'base' => null
	];
	$fallo = true;
}

http_response_code($fallo ? 503 : 200);

echo json_encode(
	$resultado,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
);