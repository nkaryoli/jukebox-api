# Bloque 20 · Entrega y presentación técnica

## Qué cambia y por qué
La ampliación de RA2 queda lista para entregar, probar y defender. Se conservan las etapas previas, las páginas clásicas y la nueva gestión AJAX.

## Qué se entrega
- Primera etapa (puerto 8090), sin modificar.
- Segunda etapa (puerto 8091) en docker/, con el CRUD clásico, el reproductor y la gestión AJAX.
- docker/data-ejemplo/ con datos ficticios (canciones.json y usuarios.json vacío).
- docker/.env.example con valores ficticios. No se entrega ningún .env real.
- Notas docs/13 a docs/20 e informe con la sección RA2.

## Cómo preparar una copia
1. Copiar el proyecto a una carpeta nueva.
2. En docker/, copiar .env.example a .env y poner contraseñas ficticias.
3. Crear docker/data/ y copiar dentro los JSON de docker/data-ejemplo/.
4. Ejecutar: docker compose up -d --build
5. Comprobar: docker compose ps (web Up; postgres y mariadb healthy).
6. Abrir http://localhost:8091/register.php, crear una cuenta ficticia, iniciar sesión y entrar en la home.

## Qué probar y resultado esperado
- Login, home, reproductor y CRUD clásico funcionan.
- Gestión AJAX: alta, edición y borrado sin recargar la página.
- Sin sesión, la API responde 401 en JSON.
- Un POST sin token responde 403 y no cambia el archivo.
- Los cambios hechos en una pantalla aparecen en las otras al volver a consultar.

## Limitaciones del prototipo
- El catálogo está en un archivo JSON con bloqueo; no es una base de datos.
- Los audios los sirve Apache sin control de acceso de PHP.
- El reproductor abierto no se sincroniza solo: hay que recargar la home.
- No hay roles: todos los usuarios autenticados comparten el catálogo.