<?php

require_once __DIR__ . '/lib/sesion.php';

$errores = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$recibido = $_POST['csrf'] ?? null;

	if ( !is_string($recibido) || !hash_equals($_SESSION['csrf'], $recibido) ) {
		http_response_code(403);
		exit('Error 403: token CSRF no válido.');
	}
	
	$emailRecibido = $_POST['email'] ?? null;
	$passwordRecibido = $_POST['password'] ?? null;

	if (
    !is_string($emailRecibido) ||
    !is_string($passwordRecibido)
	) {
    $errores[] = 'Credenciales no válidas.';
	} else {
    $email = strtolower(trim($emailRecibido));
    $password = $passwordRecibido;
	}	
}
?>