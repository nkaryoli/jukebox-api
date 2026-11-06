<?php

require_once __DIR__ . '/lib/sesion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Método no permitido.');
}

$recibido = $_POST['csrf'] ?? null;

if ( !is_string($recibido) || !hash_equals($_SESSION['csrf'], $recibido) ) {
	http_response_code(403);
	exit('Error 403: token CSRF no válido.');
}

$parametros = session_get_cookie_params();

$_SESSION = [];

setcookie(session_name(), '', [
	'expires' => time() - 42000,
	'path' => $parametros['path'],
	'domain' => $parametros['domain'],
	'secure' => $parametros['secure'],
	'httponly' => $parametros['httponly'],
	'samesite' => $parametros['samesite']
]);

session_destroy();

header('Location: login.php', true, 303);
exit;