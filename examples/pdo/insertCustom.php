<?php

$class = $pdoLayer;

if(is_null($class)) {
    die('PDPLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- PDO INSERT CUSTOM ---- ' . PHP_EOL;

//--------------------------------------------


echo PHP_EOL . 'Insert custom' . PHP_EOL;
$data = [
            'name' => 'tereza-pck',
            'email' => 'email-pck',
            'phone' => 'phone',
            'password' => 'password',
            'created' => '2018-1-1',
        ];
$insert = $class->table('users', false)->insertCustom($data);

print_r($insert);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

// Delete entry
$class->table('users')->delete('tereza-pck', 'name');
echo 'Delete Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
print_r($class->errorInfo);