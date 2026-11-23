# Bloque 19 · Integrar y probar también los fallos

## Qué cambia y por qué
Las cuatro operaciones forman una pantalla coherente que convive con el jukebox y el CRUD clásico. Además se prueban los casos de error.

## Qué se ha hecho
- Enlaces entre home, gestión clásica y gestión AJAX. Las tres requieren sesión.
- Revisión con teclado: etiquetas, foco al editar y al cancelar.
- Mensajes diferenciados y éxito nunca anunciado desde finally.
- Funciones comunes: interpretarRespuesta, enviarCambio y ejecutarCambio.
- api/canciones.php devuelve una lista simple; api/catalogo.php devuelve {ok, data}. Los dos leen el mismo catálogo.
- Funciones compartidas: leer_json_bloqueado, actualizar_json_bloqueado, validarDatosCancion, categoriasPermitidas y audiosPermitidos.

## Qué probar y resultado esperado
- Un cambio hecho en una pantalla se ve en las otras al volver a consultar.
- Sin sesión: 401. Sin token: 403. GET a create: 405 con Allow. Cuerpo no JSON: 415. JSON mal escrito: 400. Campos inválidos: 422. Id inexistente: 404.
- JSON dañado: 500 y el archivo no se sustituye por [].
- Sin red: mensaje comprensible.

## Incidencia
- Síntoma: la home no mostraba canciones; la consola decía "El catálogo recibido no es una lista válida de canciones".
- Causa: api/canciones.php devolvía {ok, data} y el reproductor esperaba una lista directa.
- Corrección: canciones.php vuelve a devolver la lista simple y playlist.js lee la respuesta sin data.data.