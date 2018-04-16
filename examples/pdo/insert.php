<?php

$class = $pdoLayer;

if(is_null($class)) {
    die('PDPLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- PDO INSERT ---- ' . PHP_EOL;
//--------------------------------------------

$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => 'data_created',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . ' Insert Fail: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
//--------------------------------------------

echo PHP_EOL . ' Insert Fail 1' . PHP_EOL;
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            //'city' => 'city',
            'created' => '2018-18-18',
        ];
$insert = $class->table('users')->insert($data, true);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
//--------------------------------------------

echo PHP_EOL . ' Insert Fail 2' . PHP_EOL;
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            // 'address' => 'address',
            //'city' => 'city',
            'created' => '2018-1-1',
        ];
$insert = $class->table('users')->insert($data, true);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-13-03',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . ' Insert Fail: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '0000-00-00',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . ' Insert Fail: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-03-03',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . ' Insert Pass: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza',
            'email' => 'email',
            'phone' => 'phone',
            'password' => 'password',
            'address' => 'address',
            'city' => 'city',
            'created' => '2018-03-03',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . ' Insert Fail: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

// Delete entry
$class->table('users')->delete(1);