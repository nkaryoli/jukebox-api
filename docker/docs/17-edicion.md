# Bloque 17 · Editar la canción elegida

## Qué cambia y por qué
El formulario del alta se reutiliza para editar. La dificultad es mantener la identidad de la ficha: editar no debe crear otra canción ni alterar otra fila.

## Qué se ha hecho
- Editar lee dataset.id, lo convierte a número y busca la ficha por id en la lista local.
- El id se conserva en la variable idEditando; el botón pasa a "Guardar cambios" y aparece Cancelar.
- Con idEditando se envía action=update con el id; sin él, action=create.
- PHP valida el id y los campos, busca la ficha dentro del bloqueo y la sustituye conservando el id.
- Si la ficha ya no existe, 404 y no se vuelve a crear.
- Cancelar no envía ninguna petición.

## Qué probar y resultado esperado
- Cambiar título y duración: 200, mismo id y mismo número de fichas.
- Cancelar: ninguna petición y formulario en modo alta.
- Eliminar la ficha desde otra pestaña y guardar la edición: 404.