<?php

$class = $pdoLayer;

if(is_null($class)) {
    die('PDPLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- PDO JOIN ---- ' . PHP_EOL;

//--------------------------------------------

echo PHP_EOL . 'Join' . PHP_EOL;

$fields = 'users.uid, users.name, users.address, user_data.cat_name, user_data.dog_name';

$class->table('users', false)->select($fields)->join('user_data', 'users.uid = user_data.user_id')->get();
$res = $class->execute();
print_r($res);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

