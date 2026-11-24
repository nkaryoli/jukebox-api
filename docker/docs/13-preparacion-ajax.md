# Bloque 13 · Preparar la ampliación

## Qué cambia y por qué
Se añade una pantalla privada y una API nuevas para gestionar el catálogo con AJAX, de modo que la tabla se actualice sin recargar la página. Se conserva el CRUD clásico para poder comparar una petición que recarga el documento con otra que solo actualiza la tabla. Las dos versiones usan el mismo catálogo y las mismas reglas.

## Punto de recuperación
Antes de modificar nada se comprobó que se podía iniciar sesión, consultar el catálogo y reproducir un audio. Después se creó la rama feat/practica2 y se guardó un commit con el estado del proyecto antes de comenzar la ampliación AJAX.

## Estructura nueva
- docker/src/canciones-ajax.php: nueva pantalla privada.
- docker/src/api/catalogo.php: nueva API para el CRUD.
- docker/src/scripts/catalogo-ajax.js: peticiones y tabla.
- docker/src/lib/: se reutilizan las funciones existentes.
- docker/data/canciones.json y usuarios.json: los mismos datos privados, fuera de la carpeta pública.
- docs/13-preparacion-ajax.md: notas de este bloque.

## Estado de los servicios
Se arrancó la segunda etapa desde la carpeta docker con docker compose up -d y se comprobó con docker compose ps:
- web: Up, puerto 8091.
- postgres: healthy.
- mariadb: healthy.
La primera etapa (puerto 8090) no se ha modificado. Las bases de datos siguen en Compose, pero esta ampliación no las usa para guardar canciones.

## Qué probar y resultado esperado
- Login, home, reproductor y CRUD clásico funcionan en http://localhost:8091.
- Los tres servicios están activos.
- canciones.json no está dentro de src: no existe un catálogo paralelo.

## Resultado obtenido
El entorno quedó preparado sin perder la etapa anterior. La home y el CRUD clásico siguen funcionando antes de añadir AJAX.