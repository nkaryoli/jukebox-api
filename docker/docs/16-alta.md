# Bloque 16 · Alta segura con JSON

## Qué cambia y por qué
Se añade el formulario de alta en canciones-ajax.php y la acción create en api/catalogo.php. PHP toma la decisión final sobre quién envía y si los datos son válidos.

## Qué se ha hecho
- Formulario con label, required, maxlength, select y type=number (min 1, max 1800, step 1).
- Token CSRF publicado en una meta y enviado en la cabecera X-CSRF-Token.
- La API comprueba sesión, método, token, tipo de contenido, JSON y valores, en ese orden.
- El id lo asigna PHP dentro de actualizar_json_bloqueado().
- JavaScript actualiza la tabla solo tras recibir 201 y ok: true.

## Qué probar y resultado esperado
- POST sin token: 403 y el archivo no cambia.
- Duración negativa: 422. Categoría no admitida: 422.
- JSON mal formado: 400. Tipo de contenido distinto: 415. GET a create: 405 con Allow: POST.
- Alta válida: 201, fila nueva sin recarga, formulario vacío, datos conservados tras recargar.
- Doble clic: una sola petición.
- Fallo de red: mensaje de que no se ha podido confirmar y datos conservados.