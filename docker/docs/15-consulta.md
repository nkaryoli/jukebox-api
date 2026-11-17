# Bloque 15 · Consultar desde PHP, pedir la lista con fetch y mostrar una tabla fiable

## Qué cambia y por qué
Se construye primero la lectura para comprobar sesión, ruta y formato antes de añadir operaciones que modifican datos.

## Qué se ha hecho
- api/catalogo.php: comprueba la sesión (401 en JSON), valida action y atiende list solo por GET.
- canciones-ajax.php: página privada con área de mensajes y tabla.
- scripts/catalogo-ajax.js: cargarCatalogo() pide la lista con fetch y renderizarCanciones() dibuja la tabla con textContent.

## Qué probar y resultado esperado
- Con sesión, action=list devuelve 200 y {"ok":true,"data":[...]}.
- Sin sesión devuelve 401 en JSON, no una página de login.
- action desconocida o action[] devuelve 400.
- La tabla muestra las canciones y, si no hay ninguna, el mensaje "Todavía no hay canciones".
- Un título con < o > se muestra como texto.