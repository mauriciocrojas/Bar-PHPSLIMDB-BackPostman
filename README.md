Iniciamos sesion con los datos de la base

![mysql5](https://i.ibb.co/gF2nN9g/Screenshot-at-Mar-29-19-52-39.png)

Desde el panel de este sitio vamos a poder administrar las diferentes bases, crear y borrar tablas y hacer consultas SQL.

![mysql6](https://i.ibb.co/4sY1XNF/Screenshot-at-Mar-29-19-53-10.png)


## Requisitos para correr localmente

- Instalar PHP o XAMPP (https://www.php.net/downloads.php o https://www.apachefriends.org/es/download.html)
- Instalar Composer desde https://getcomposer.org/download/ o por medio de CLI:

```sh
php -r "copy('//getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
```

## Correr localmente via XAMPP

- Copiar proyecto dentro de la carpeta htdocs

```sh
C:\xampp\htdocs\
```
- Acceder por linea de comandos a la carpeta del proyecto y luego instalar Slim framework via Compose

```sh
cd C:\xampp\htdocs\<ruta-del-repo-clonado>
composer update
```
- En el archivo index.php agregar la siguiente linea debajo de `AppFactory::create();`

```sh
// Set base path
$app->setBasePath('/app');
```
- Abrir desde http://localhost/app ó http://localhost:8080/app (depende del puerto configurado en el panel del XAMPP)

## Correr localmente via PHP

- Acceder por linea de comandos a la carpeta del proyecto y luego instalar Slim framework via Compose

```sh
cd C:\<ruta-del-repo-clonado>
composer update
php -S localhost:666 -t app
```

- Abrir desde http://localhost:666/

## Archivo .env localmente

Crear dentro de la carpeta `/app/` el archivo `.env` tomando de referencia `.env.example`

Agregamos los siguientes datos Clave -> Valor:

```sh
MYSQL_HOST=remotemysql.com (campo "Server" de los datos que guardamos al crear la base en remotemysql.com)
MYSQL_PORT=3306 (campo "Port" de los datos que guardamos al crear la base en remotemysql.com)
MYSQL_USER=elcNx8VTCx (campo "Username" de los datos que guardamos al crear la base en remotemysql.com)
MYSQL_PASS=1234 (campo "Password" de los datos que guardamos al crear la base en remotemysql.com)
MYSQL_DB=elcNx8VTCx (campo "Database Name" de los datos que guardamos al crear la base en remotemysql.com)
```

