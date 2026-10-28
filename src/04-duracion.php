
<?php
$duration = 185;

$minutes = intdiv($duration, 60);
$seconds = $duration % 60;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Duración de la canción</title>
</head>
<body>
    <h1>Duración de la canción</h1>
    <p>Duración total: <?php echo $duration; ?> segundos.</p>
    <p>
        Equivale a 
        <?php echo $minutes; ?> minutos y
        <?php echo $seconds; ?> segundos.
    </p>
</body>
</html>
