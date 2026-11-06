<?php

require_once __DIR__ . '/lib/sesion.php';
require_once __DIR__ . '/lib/almacen-json.php';

$errores = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recibido = $_POST['csrf'] ?? null;

    if ( !is_string($recibido) || !hash_equals($_SESSION['csrf'], $recibido) ) {
        http_response_code(403);
        exit('Error 403: token CSRF no válido.');
    }

    $emailRecibido = $_POST['email'] ?? null;
    $passwordRecibida = $_POST['password'] ?? null;

    if ( !is_string($emailRecibido) || !is_string($passwordRecibida) ) {
        $errores[] = 'Credenciales no válidas.';
    } else {
        $email = strtolower(trim($emailRecibido));
        $password = $passwordRecibida;

        try {
            $usuarios = leer_json_bloqueado('/var/www/data/usuarios.json');
            $usuarioEncontrado = null;

            foreach ($usuarios as $usuario) {
                if (
                    is_array($usuario) && isset($usuario['email']) &&
                    is_string($usuario['email']) && $usuario['email'] === $email 
                ) {
                    $usuarioEncontrado = $usuario;
                    break;
                }
            }

            if (
                $usuarioEncontrado !== null && isset($usuarioEncontrado['password_hash']) &&
                is_string($usuarioEncontrado['password_hash']) && 
                password_verify( $password, $usuarioEncontrado['password_hash'] )
            ) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $usuarioEncontrado['id'];
                $_SESSION['alias'] = $usuarioEncontrado['alias'];
                header('Location: home.php', true, 303);
                exit;
            }

            $errores[] = 'Credenciales no válidas.';
        } catch (Throwable $e) {
            $errores[] = 'No se pudo comprobar el acceso.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>
    <h1>Iniciar sesión</h1>
    <?php foreach ($errores as $error): ?>
        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>
    <form method="post">
        <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>"
        >
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
        <br/>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>