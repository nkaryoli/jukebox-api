<?php
$title = '<b>Demo</b>';
$artist = 'Grupo del aula';
$duration = 185;
$volume = 0.5;
$available = true;

define('APP_NAME', 'Jukebox de la asociación');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
</head>
<body>   
    <h1><?php echo htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h1>
    <p>
        La canción 
        "<?php echo htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>" 
        está interpretada por
        <?php echo htmlspecialchars($artist, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>.
    </p>
</body>
</html>
