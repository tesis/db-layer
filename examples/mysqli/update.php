<?php

$class = $mysqliLayer;
// print_r($class);

if(is_null($class)) {
    die('MySqliLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- UPDATE ---- ' . PHP_EOL;

//--------------------------------------------
echo PHP_EOL . 'Update non-existing record with wrong data - Fail 1' . PHP_EOL;
$data = [
            'uid' => 1,
            'name' => 'tereza simcic',
            'created' => 'date',
        ];
$insert = $class->table('users')->update($data, true);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
echo PHP_EOL . 'Update non-existing record Fail 2' . PHP_EOL;
$data = [
            'uid' => 1,
            'name' => 'tereza simcic',
            'created' => '2018-05-03',
        ];
$insert = $class->table('users')->update($data, true);
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
            'created' => '2018-02-02',
        ];
$insert = $class->table('users')->insert($data, true);
echo PHP_EOL . 'New created record for testing updates: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza simcic',
            'created' => '2018-05-03',
        ];
$insert = $class->table('users')->update($data, true);
echo PHP_EOL . 'Update existing record - Pass: ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
$data = [
            'uid' => 1,
            'name' => 'tereza simcic',
            'created' => 'wrong date',
        ];
$insert = $class->table('users')->update($data, true);
echo PHP_EOL . 'Update existing record - Fail (wrong date): ' . PHP_EOL;
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;


// Delete entry
$class->table('users')->delete(1);