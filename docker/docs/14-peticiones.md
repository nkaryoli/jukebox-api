# Bloque 14 · Entender la nueva petición y acordar las operaciones

## Qué cambia y por qué
Hasta ahora, un formulario enviaba los datos y el navegador cargaba otra página. Con fetch, JavaScript hace la petición en segundo plano, recibe JSON y modifica solo la tabla.

## Recorrido de una consulta
Usuario → JavaScript con fetch (navegador) → api/catalogo.php (PHP, servidor) → canciones.json (privado, /var/www/data) → respuesta JSON → JavaScript actualiza la tabla.

## Qué espera recibir cada dirección
- api/canciones.php: devuelve una lista simple. Su formato no cambia porque el reproductor de la home usa songs[i].src.
- api/catalogo.php: devuelve un objeto con ok. Si funciona, data contiene la lista actualizada. Si falla, ok es false y error contiene el mensaje.
- Las dos leen el mismo archivo con las funciones de lib/almacen-json.php. No hay dos catálogos.

## Operaciones
| Operación | Petición | Datos enviados | Éxito |
| --- | --- | --- | --- |
| Consultar | GET api/catalogo.php?action=list | Ninguno | 200 y lista |
| Crear | POST api/catalogo.php?action=create | title, artist, category, duration, src | 201 y lista |
| Editar | POST api/catalogo.php?action=update | id y los cinco campos | 200 y lista |
| Eliminar | POST api/catalogo.php?action=delete | id | 200 y lista |

## Errores
| Código | Cuándo | Mensaje |
| --- | --- | --- |
| 400 | JSON mal escrito o acción desconocida | El cuerpo no es un JSON válido. |
| 401 | No hay sesión | Debes iniciar sesión. |
| 403 | Token CSRF ausente o incorrecto | Token de protección no válido. |
| 404 | La canción no existe | La canción no existe. |
| 405 | Método no permitido (cabecera Allow) | Método no permitido para esta acción. |
| 415 | El cuerpo no es application/json | El contenido debe enviarse como application/json. |
| 422 | Valores no válidos | La duración debe ser un entero entre 1 y 1800 segundos. |
| 500 | Fallo al leer o guardar el catálogo | No se pudo acceder al catálogo. |

## Qué probar y resultado esperado
- Consultar sin sesión: 401, "Debes iniciar sesión."
- Editar un id inexistente: 404, "La canción no existe."
- Duración negativa: 422, "La duración debe estar entre 1 y 1800 segundos."