# Jukebox de la Asociación - Práctica Acumulativa PHP (RA1)

Aplicación web musical completa con servidor en PHP, persistencia privada en archivos JSON y cliente interactivo tipo Jukebox (Sound Wave). Proyecto individual desarrollado para la asignatura de Desenvolupament Web en Entorn Servidor (0613).

---

## Requisitos del sistema

- **Docker Desktop** (con soporte para Docker Compose y WSL2 en Windows).
- **Navegador web moderno** (Google Chrome, Firefox o Edge).
- **Git** (opcional, para control de versiones).
- No se requiere PHP instalado en la máquina anfitrión (la ejecución se realiza íntegramente dentro de los contenedores Docker).

---

## Estructura de carpetas

```text
asociacion-jukebox-php/
├── compose.yaml          # Etapa inicial (puerto 8090)
├── src/                  # Código inicial de aprendizaje (pasos 01 a 10 y lib/)
├── data-ejemplo/         # Plantillas de datos para clonación limpia
│   ├── canciones.json    # Catálogo de canciones de muestra
│   └── usuarios.json     # Lista vacía de usuarios inicial ([])
├── .env.example          # Plantilla de variables de entorno (sin secretos)
├── .gitignore            # Exclusión de .env y datos privados
├── .dockerignore         # Exclusión de datos privados del contexto Docker
└── docker/               # Etapa ampliada multiservicio (puerto 8091)
    ├── compose.yaml      # Orquestación de web (Apache+PHP), PostgreSQL y MariaDB
    ├── Dockerfile        # Imagen personalizada con extensiones pdo_pgsql y pdo_mysql
    ├── .env.example      # Ejemplo de variables de base de datos
    ├── .env              # Variables locales de entorno (no versionadas)
    ├── apache/
    │   └── jukebox.conf  # VirtualHost de Apache (DocumentRoot /var/www/html)
    ├── data/             # Persistencia JSON privada (fuera de DocumentRoot)
    │   ├── canciones.json# Catálogo dinámico de canciones
    │   └── usuarios.json # Cuentas registradas con contraseñas hash
    └── src/              # Código fuente de producción servido por Apache
        ├── index.php / 01-... # Pasos de desarrollo
        ├── login.php     # Autenticación y apertura de sesión
        ├── register.php  # Registro con validación y hash bcrypt
        ├── home.php      # Panel privado e interfaz integrada del Jukebox
        ├── logout.php    # Cierre de sesión seguro por POST
        ├── canciones.php # Consulta y administración del catálogo (CRUD)
        ├── cancion-nueva.php    # Alta de canciones
        ├── cancion-editar.php   # Modificación de canción por ID
        ├── cancion-eliminar.php # Confirmación y borrado por ID
        ├── api/
        │   └── canciones.php    # Endpoint REST que sirve el catálogo en JSON
        ├── lib/          # Librerías comunes (sesion.php, acceso.php, almacen-json.php, canciones.php)
        ├── assets/       # CSS (style.css), imágenes y portadas
        ├── audio/        # Archivos de audio MP3 autorizados
        └── scripts/      # Módulos JavaScript del Jukebox (player, ui, playlist, etc.)
```

---

## Procedimiento de preparación y arranque

### 1. Preparación de una copia limpia

Al clonar o preparar el proyecto por primera vez en un nuevo equipo:

1. **Configurar el entorno:**
   Copiar `.env.example` como `.env` dentro de la carpeta `docker/`:
   ```bash
   cp docker/.env.example docker/.env
   ```
2. **Inicializar los datos privados:**
   Copiar las plantillas desde `data-ejemplo/` hacia `docker/data/` (**únicamente al preparar una copia nueva, nunca sobre datos que se deseen conservar**):
   ```bash
   cp data-ejemplo/canciones.json docker/data/canciones.json
   cp data-ejemplo/usuarios.json docker/data/usuarios.json
   ```

### 2. Arranque con Docker Compose

Acceder al directorio de la segunda etapa y levantar los contenedores:

```powershell
cd docker
docker compose config --quiet          # Valida el YAML
docker compose up -d --build           # Construye e inicia los servicios
docker compose ps                      # Comprueba que web está Up y las BD healthy
```

Para detener los contenedores conservando los datos:
```powershell
docker compose stop
```

---

## Direcciones URL de uso

| Dirección | Descripción | Acceso |
| :--- | :--- | :--- |
| `http://localhost:8091/login.php` | Inicio de sesión | Público |
| `http://localhost:8091/register.php` | Registro de una cuenta ficticia | Público |
| `http://localhost:8091/home.php` | Jukebox musical interactivo | Privado (requiere sesión) |
| `http://localhost:8091/canciones.php` | Catálogo y administración CRUD | Privado (requiere sesión) |
| `http://localhost:8091/api/canciones.php` | Endpoint JSON para el cliente | Privado (HTTP 401 si no hay sesión) |
| `http://localhost:8090/01-bienvenida.php` | Etapa inicial de prueba | Informativo |

---

## Cómo crear la primera cuenta ficticia

1. Abrir en el navegador: `http://localhost:8091/register.php`.
2. Completar los campos con datos ficticios de prueba:
   - **Alias:** entre 2 y 30 caracteres (ej. `Melomano`).
   - **Correo electrónico:** formato válido (ej. `prueba@asociacion.local`).
   - **Contraseña:** entre 12 y 64 caracteres (ej. `claveSegura1234`).
   - **Repetir contraseña:** la misma clave anterior.
3. Pulsar en **Registrar**. El servidor validará los tipos, evitará duplicados y generará el hash seguro con `password_hash()`.
4. El sistema redirigirá con HTTP 303 a `login.php`. Introducir el correo y la contraseña para iniciar sesión y entrar a `home.php`.