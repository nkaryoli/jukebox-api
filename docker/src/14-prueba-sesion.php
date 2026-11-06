<?php

require_once __DIR__ . '/lib/sesion.php';

echo '<pre>';

var_dump($_SESSION['usuario_id'] ?? null);
var_dump($_SESSION['alias'] ?? null);

echo '</pre>';