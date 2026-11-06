<?php

function duracionLegible(int $total): string
{
    $minutes = intdiv($total, 60);
    $seconds = $total % 60;

    return $minutes . ' minutos y ' . $seconds . ' segundos';
}

function validarDatosCancion( array $datos, array $allowedCategories, array $allowedSources ): array {
    $errors = [];

    $title = $datos['title'] ?? '';
    $artist = $datos['artist'] ?? '';
    $category = $datos['category'] ?? '';
    $duration = $datos['duration'] ?? '';
    $src = $datos['src'] ?? '';

    foreach ([
        'title' => &$title,
        'artist' => &$artist,
        'category' => &$category,
        'duration' => &$duration,
        'src' => &$src
    ] as $field => &$value) {

        if (!is_string($value)) {
            $errors[$field] = 'El campo no es válido.';
            $value = '';
        }
    }

    unset($value);

    $title = trim($title);
    $artist = trim($artist);

    if ($title === '') {
        $errors['title'] = 'El título es obligatorio.';
    } elseif (!preg_match('/^.{1,80}$/us', $title)) {
        $errors['title'] = 'El título no puede superar los 80 caracteres.';
    }

    if ($artist === '') {
        $errors['artist'] = 'El artista es obligatorio.';
    } elseif (!preg_match('/^.{1,80}$/us', $artist)) {
        $errors['artist'] = 'El artista no puede superar los 80 caracteres.';
    }

    if (!in_array($category, $allowedCategories, true)) {
        $errors['category'] = 'Selecciona una categoría válida.';
    }

    $durationValue = filter_var($duration, FILTER_VALIDATE_INT);

    if ($duration === '') {
        $errors['duration'] = 'La duración es obligatoria.';
    } elseif ($durationValue === false) {
        $errors['duration'] = 'La duración debe ser un número entero.';
    } elseif ($durationValue < 1 || $durationValue > 1800) {
        $errors['duration'] = 'La duración debe estar entre 1 y 1800 segundos.';
    }

    if (!in_array($src, $allowedSources, true)) {
        $errors['src'] = 'Selecciona un audio válido.';
    }

    return [
        'data' => [
            'title' => $title,
            'artist' => $artist,
            'category' => $category,
            'duration' => $durationValue,
            'src' => $src
        ],
        'errors' => $errors
    ];
}

?>