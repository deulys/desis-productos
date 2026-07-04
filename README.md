# Sistema de Registro de Productos

Prueba de diagnostico para DESIS.

Autor: Deulys Vetancourt

## Descripcion

Aplicacion sencilla para registrar productos desde un formulario web.

El proyecto esta hecho con PHP limpio, HTML, CSS nativo y JavaScript/AJAX nativo,
sin frameworks. La informacion se guarda en PostgreSQL y los datos de bodega,
sucursal y moneda se cargan desde la base de datos.

Esta rama incluye Docker para levantar la aplicacion y PostgreSQL sin instalar
la base de datos localmente.

## Versiones usadas

- PHP 8.1 o superior
- PostgreSQL 14 o superior
- Docker y Docker Compose

## Estructura del proyecto

```text
/index.php                         Pantalla principal del formulario
/assets/css/styles.css             Estilos del formulario
/assets/js/app.js                  Validaciones y llamadas AJAX
/api/catalogos.php                 Carga bodegas y monedas
/api/sucursales.php                Carga sucursales segun la bodega
/api/guardar_producto.php          Valida y guarda el producto
/api/helpers.php                   Funciones comunes para responder JSON y validar IDs
/config/database.php               Conexion PDO a PostgreSQL
/sql/crear_tablas.sql              Creacion de tablas
/sql/cargar_datos_iniciales.sql    Datos iniciales para los selects
/Dockerfile                        Imagen PHP para correr la aplicacion
/docker-compose.yml                Levanta la aplicacion y PostgreSQL
```

## Instalacion con Docker

Levantar los contenedores:

```bash
docker compose up --build
```

Abrir en el navegador:

```text
http://localhost:8080
```

Docker Compose crea una base PostgreSQL llamada `desis_productos` y carga los
archivos de la carpeta `sql/` automaticamente la primera vez que se crea el
volumen de la base de datos.

La aplicacion queda publicada en el puerto `8080` para no chocar con un PHP
local que ya este usando el puerto `8000`.

Para reiniciar la base de datos desde cero:

```bash
docker compose down -v
docker compose up --build
```

Para apagar los contenedores:

```bash
docker compose down
```

## Instalacion local sin Docker

Entrar a la carpeta del proyecto:

```bash
cd /Users/mac/Downloads/desis-productos
```

Crear la base de datos:

```bash
createdb desis_productos
```

Crear las tablas:

```bash
psql -d desis_productos -f sql/crear_tablas.sql
```

Cargar los datos iniciales:

```bash
psql -d desis_productos -f sql/cargar_datos_iniciales.sql
```

Levantar el proyecto:

```bash
php -S localhost:8000
```

Abrir:

```text
http://localhost:8000
```

## Configuracion de base de datos

Por defecto la aplicacion intenta conectarse con estos datos:

```text
DB_HOST=localhost
DB_PORT=5432
DB_NAME=desis_productos
DB_USER=postgres
DB_PASSWORD=postgres
```

Si PostgreSQL usa otro usuario o clave, se puede indicar al levantar el servidor.
Por ejemplo, en macOS con Homebrew normalmente el usuario de PostgreSQL es el
mismo usuario del sistema:

```bash
psql -d desis_productos -c "SELECT current_user;"
```

Si el usuario mostrado es `mac`, levantar asi:

```bash
DB_HOST=127.0.0.1 DB_NAME=desis_productos DB_USER=mac DB_PASSWORD='' php -S localhost:8000
```

## Verificacion

Ver tablas:

```bash
psql -d desis_productos -c "\dt"
```

Ver datos iniciales:

```bash
psql -d desis_productos -c "SELECT * FROM bodegas;"
psql -d desis_productos -c "SELECT * FROM sucursales;"
psql -d desis_productos -c "SELECT * FROM monedas;"
```

La tabla `productos` empieza vacia. Se llena cuando se guarda un producto desde
el formulario.

## Validaciones implementadas

- Codigo obligatorio.
- Codigo con letras y numeros, sin caracteres especiales.
- Codigo entre 5 y 15 caracteres.
- Codigo unico en base de datos.
- Nombre obligatorio entre 2 y 50 caracteres.
- Bodega obligatoria cargada desde base de datos.
- Sucursal obligatoria cargada segun la bodega seleccionada.
- Moneda obligatoria cargada desde base de datos.
- Precio obligatorio, positivo y con hasta dos decimales.
- Al menos dos materiales seleccionados.
- Descripcion obligatoria entre 10 y 1000 caracteres.
- Validaciones en JavaScript y tambien en PHP.

## Entrega

Subir el proyecto completo a GitHub o similar.
No comprimir el proyecto, segun lo solicitado en la prueba.
