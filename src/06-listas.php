
<?php
echo '<h2>Lista con for</h2>';
for ($i = 1; $i <= 4; $i++) {
    echo $i . '<br>';
}

echo '<h2>Lista con while</h2>';
$i = 1;
while ($i <= 4) {
    echo $i . '<br>';
    $i++;
}

echo '<h2>Lista de canciones con foreach</h2>';

$titulos = [
    'Sesión de prueba',
    'Noche de rock',
    'Banda sonora'
];

foreach ($titulos as $indice => $titulo) {
    echo ($indice + 1) . '. ' . htmlspecialchars($titulo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '<br>';
}
