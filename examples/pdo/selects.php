<?php

$class = $pdoLayer;

if(is_null($class)) {
    die('PDPLayer class is null!');
}

echo PHP_EOL . __FILE__ . ' --- PDO SELECT ---- ' . PHP_EOL;

//--------------------------------------------

echo PHP_EOL . 'Select by sql statement: ' . PHP_EOL;
$sql = "SELECT * from users";
$res = $class->executeSql($sql);

print_r($res);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//------------------------- INSERT few records for test -------------------
$datas = [
    [
        'uid' => 1,
        'name' => 'tereza1',
        'email' => 'email1',
        'phone' => 'phone1',
        'password' => 'password1',
        'address' => 'address',
        'city' => 'city1',
        'created' => '2018-02-02 11:15',
    ],
    [
        'uid' => 2,
        'name' => 'tereza2',
        'email' => 'email2',
        'phone' => 'phone2',
        'password' => 'password2',
        'address' => 'address',
        'city' => 'city2',
        'created' => '2018-02-02 13:15',
    ],
    [
        'uid' => 3,
        'name' => 'tereza3',
        'email' => 'email3',
        'phone' => 'phone3',
        'password' => 'password3',
        'address' => 'address',
        'city' => 'city3',
        'created' => '2018-02-02 12:15',
    ]
];

foreach ($datas as $data) {
    $insert = $class->table('users')->insert($data, true);
}

echo PHP_EOL . 'New created record for testing selects inserted ' . PHP_EOL;


//--------------------------------------------
echo PHP_EOL . 'Build select - where - get all: ' . PHP_EOL;
$class->table('users')->select('uid, name, email')->where('uid', 1)->get();
$res = $class->execute();
print_r($res);
// print_r($class);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
echo PHP_EOL . 'Build select - where - all: ' . PHP_EOL;
$class->table('users')->select('uid, name, email, address')->where('address', 'address')->all();
$all = $class->execute();
echo $class->sql . PHP_EOL;
print_r($all);
// $sql = 'SELECT uid,name,email,address FROM users WHERE address="address"';
// $res = $class->executeSql($sql);

print_r($res);
echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
echo PHP_EOL . 'Build select - where - first: ' . PHP_EOL;

$class->table('users')->select('uid, name, email, address')->where('address', 'address')->first();
// $class->table('users')->select('uid, name, email, address')->where('uid', 1)->first();
// $class->table('users')->select('uid, name, email, address')->first();
$first = $class->execute();
print_r($first);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;
//--------------------------------------------
echo PHP_EOL . 'Build select - where - get 2: ' . PHP_EOL;
$class->table('users')->select('uid, name, email')->where('address', 'address')->get(2);
$res = $class->execute();
print_r($res);
// print_r($class);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------

echo PHP_EOL . 'Build select - where - orWhere get 2: ' . PHP_EOL;
$class->table('users')->select('uid, name, email,address')->where('address', 'address')->orWhere('email', 'email1')->get(2);
$res = $class->execute();
print_r($res);
// print_r($class);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------

echo PHP_EOL . 'Build select - where - andWhere get 2: ' . PHP_EOL;
$class->table('users')->select('uid, name, email,address')->where('address', 'address')->andWhere('email', 'email1')->get(2);
$res = $class->execute();
print_r($res);
// print_r($class);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;

//--------------------------------------------
# FIXME count('uid'), distinct, sum, min, ...
/*echo PHP_EOL . 'Build select - where - orWhere get 2: ' . PHP_EOL;
$class->table('users')->select('uid,name,address')->groupBy('address')->get();
echo $class->sql;
$res = $class->execute();
print_r($res);
// print_r($class);

echo 'Error: ' . $class->error . ' Affected rows: ' . $class->affectedRows . PHP_EOL . PHP_EOL;*/

//--------------------------------------------

// Delete entry
foreach ($datas as $data) {
    $class->table('users')->delete($data['uid']);
}

