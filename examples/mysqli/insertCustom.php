<?php

$class = $mysqliLayer;
// print_r($class);

if(is_null($class)) {
    die('MySqliLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- INSERT CUSTOM ---- ' . PHP_EOL;

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
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;