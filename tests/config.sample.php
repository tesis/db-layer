<?php // config.php

/*
|--------------------------------------------------------------------------
| Testing variables
|--------------------------------------------------------------------------
|
| Change variables according to your VM settings
| Compare settings with the one already defined in
| /etc/apache2/sites-enabled/25-msg.cong
| in case something is not OK / missing
|
*/

return $config = [
    'dbHost' => 'localhost',
    'dbUser' => 'user',
    'dbPass' => 'pass',
    'dbName' => 'db',
    'dbCharset' => 'utf8',
    'mapperDir' => 'mappers',
    'mapperFile' => 'dbTablesMapper',
    'mapperType' => 'json',
    'contactEmail' => 'tereza.simcic@gmail.com',
    'environment' => 'dev',
    'debug' => true
];


