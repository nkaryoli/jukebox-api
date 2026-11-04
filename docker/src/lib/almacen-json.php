<?php

$text = file_get_contents('/var/www/data/canciones.json');

if ($text === false) {
	throw new RuntimeException('No se pudo leer el catálogo de canciones.');
}

$songs = json_decode($text, true, 512, JSON_THROW_ON_ERROR);