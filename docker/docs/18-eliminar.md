# Bloque 18 · Eliminar sin confundir fichas

## Qué cambia y por qué
Se añade la última operación del CRUD. El cliente confirma qué ficha quiere quitar. Eliminar una ficha no borra su archivo de audio.

## Qué se ha hecho
- Eliminar lee dataset.id, lo valida, busca la ficha por id y pide confirmación con el título.
- Si se cancela, no se envía nada.
- POST a action=delete con el token y {id}. Las acciones se deshabilitan mientras se espera.
- PHP busca por id dentro de actualizar_json_bloqueado(), quita la ficha con unset() y aplica array_values() sin renumerar los id.
- Si el id no existe, 404. No se usa unlink.
- La tabla se actualiza solo tras recibir 200 y ok: true.
- Si se estaba editando esa ficha, se sale del modo edición.

## Qué probar y resultado esperado
- Eliminar una ficha intermedia: 200 y los demás id intactos.
- Editar la ficha que quedó debajo: se modifica la correcta.
- Cancelar la confirmación: ninguna petición.
- Eliminar una ficha ya borrada en otra pestaña: 404.
- Eliminar la última ficha: aparece "Todavía no hay canciones".