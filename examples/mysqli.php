<?php
echo __FILE__ . PHP_EOL;

// print_r($config);

use Tesis\DBLayer\Database\MySqliLayer;

$conn = null;
$mysqliLayer = null;
$pdoLayer = null;

if(is_null($conn)) {
    $mysqliLayer = new MysqliLayer($config);
    if($mysqliLayer->error === '') {
        echo PHP_EOL . ' conn is active ' . PHP_EOL;
        // Assign globaly
        $conn = $mysqliLayer->conn;
    } else {
        echo PHP_EOL . ' conn NOT active: '  . $mysqliLayer->error . PHP_EOL;
    }
    // print_r($conn);
}
else {
    echo PHP_EOL . ' conn NOT active. Create new! ' . PHP_EOL;
    // print_r($conn);
}

$class = new MysqliLayer($config);
echo PHP_EOL . ' no connection - set database ' .PHP_EOL;
$class->setDatabase('test_db');
if(!($class->conn)) {
    echo 'NOT Class DB error: ' . $class->error . PHP_EOL;
    print_r($class->errorInfo);
    echo ' END ' . PHP_EOL;
}
echo 'Class error: ' . $class->error . PHP_EOL;

include_once __DIR__ . '/mysqli/insert.php';
include_once __DIR__ . '/mysqli/update.php';
include_once __DIR__ . '/mysqli/delete.php';
include_once __DIR__ . '/mysqli/selects.php';
include_once __DIR__ . '/mysqli/join.php';
include_once __DIR__ . '/mysqli/insertCustom.php';
