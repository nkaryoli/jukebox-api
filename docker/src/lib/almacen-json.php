<?php

function leer_json_bloqueado(string $ruta): array {
    $archivo = fopen($ruta, 'r');

    if ($archivo === false) {
        throw new RuntimeException('No se pudo abrir el archivo JSON.');
    }

    try {
        if (!flock($archivo, LOCK_SH)) {
            throw new RuntimeException('No se pudo bloquear el archivo JSON.');
        }

        $contenido = stream_get_contents($archivo);

        if ($contenido === false) {
            throw new RuntimeException('No se pudo leer el archivo JSON.');
        }

        $datos = json_decode($contenido, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($datos)) {
            throw new RuntimeException('El archivo JSON no contiene un array válido.');
        }

        return $datos;
    } finally {
        flock($archivo, LOCK_UN);
        fclose($archivo);
    }
}

function actualizar_json_bloqueado(string $ruta, callable $actualizar): array {
    $archivo = fopen($ruta, 'r+');

    if ($archivo === false) {
        throw new RuntimeException('No se pudo abrir el archivo JSON.');
    }

    $bloqueado = false;

    try {
        if (!flock($archivo, LOCK_EX)) {
            throw new RuntimeException('No se pudo bloquear el archivo JSON.');
        }

        $bloqueado = true;

        $contenido = stream_get_contents($archivo);

        if ($contenido === false) {
            throw new RuntimeException('No se pudo leer el archivo JSON.');
        }

        $datos = json_decode($contenido, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($datos)) {
            throw new RuntimeException('El archivo JSON no contiene un array válido.');
        }

        $nuevosDatos = $actualizar($datos);

        if (!is_array($nuevosDatos)) {
            throw new RuntimeException('La actualización no devolvió un array válido.');
        }

        $nuevoContenido = json_encode(
            $nuevosDatos,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_UNICODE |
            JSON_THROW_ON_ERROR
        );

        if (!rewind($archivo)) {
            throw new RuntimeException('No se pudo volver al inicio del archivo JSON.');
        }

        $bytesEscritos = fwrite($archivo, $nuevoContenido);

        if ($bytesEscritos !== strlen($nuevoContenido)) {
            throw new RuntimeException('No se pudo escribir todo el contenido JSON.');
        }

        if (!ftruncate($archivo, $bytesEscritos)) {
            throw new RuntimeException('No se pudo ajustar el tamaño del archivo JSON.');
        }

        if (!fflush($archivo)) {
            throw new RuntimeException('No se pudieron guardar los cambios en el archivo JSON.');
        }

        return $nuevosDatos;
    } finally {
        if ($bloqueado) {
            flock($archivo, LOCK_UN);
        }

        fclose($archivo);
    }
}