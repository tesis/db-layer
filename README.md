DBLayer, PHP ORM
=======================

DB layers for mysqli extension and PDO class. DBLayer is working with mappers (files holding tables' properties) to provide safer SQL operations.

## Installing DBLayer

The recommended way to install DBLayer is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of DBLayer:

```bash
composer require tesis/db-layer

# Add scripts to composer json:
"scripts": {
    "post-update-cmd": "DBLayer\\Loader::postUpdate",
    "post-install-cmd": "DBLayer\\Loader::postInstall"
}
```
or insert in your composer:

```bash
"require": {
    "tesis/db-layer": "1.0.x-dev"
},
"require-dev": {
    "phpunit/phpunit": "^6.5"
},

"scripts": {
    "post-update-cmd": "DBLayer\\Loader::postUpdate",
    "post-install-cmd": "DBLayer\\Loader::postInstall"
}

# Run: composer update / composer install
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update DBLayer using composer:

 ```bash
composer update
 ```


# DB Layer - ORM
> migrate sql files (simple SQL, see folders db/migrations, db/seeders)

> generate database mapper (default - json format, optional (.ini file, or .php file returning an array)
(examples of generated files are in tests/data folder)

> for CRUD or select statements, provided two layers: mysqli and PDO

> Start with

```sh
# Change database credentials, write env.ini file
php vendor/tesis/db-layer/init
```

## Credentials
add/change credentials in config/dbLayer.php file

## Migrate
if you database and / or tables are not yet created,
create them manually or prepare simple migration and seeder file
(examples in database/migrations and database/seeders dir)

```sh
php vendor/tesis/db-layer/migrator
```
after running migrator, new database is created
tables are created
tables are populated

### Example of configuration file

```sh
$config = [
    'dbHost' => 'localhost',
    'dbUser' => 'user',
    'dbPass' => 'pass',
    'dbName' => 'test_db',
    'dbCharset' => 'utf8',
    'mapperDir' => 'mappers',
    'mapperFile' => 'dbTablesMapper',
    'mapperType' => 'json',
    'contactEmail' => 'your_email@dot.com',
    'environment' => 'dev',
    'debug' => true
];
```
### Geneate table mapper

run the command bellow and you'll be guided through the process

```sh
php vendor/tesis/db-layer/generator
```
# Documentation / Usage

At the moment, there are examples provided in example dir.

# License

MIT License

# Last Version

1.0
