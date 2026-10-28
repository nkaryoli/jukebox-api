<?php
$duration = 241;

if ($duration === 0) {
    $message = 'Sin duración';
} elseif ($duration <= 240) {
    $message = 'Hasta cuatro minutos';
} else {
    $message = 'Más de cuatro minutos';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Estado de la canción</title>
</head>
<body>
    <h1>Estado de la canción</h1>
    <p>Duración: <?php echo $duration; ?> segundos</p>
    <p><?php echo $message; ?></p>
</body>
</html>
