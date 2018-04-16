<?php
echo __FILE__ . PHP_EOL;

// print_r($config);

use Tesis\DBLayer\Database\PDOLayer;

$conn = null;
$pdoLayer = null;

$class = new PDOLayer($config1);
echo PHP_EOL . 'PDO: no connection + set database ' .PHP_EOL;
$class->setDatabase('test_db');
if(!($class->conn)) {
    echo 'NOT Class DB errorInfo: ' . $class->error . PHP_EOL;
    print_r($class->errorInfo);
    echo ' END ' . PHP_EOL;
}
echo 'Class error: ' . $class->error . PHP_EOL;

if(is_null($conn)) {
    $pdoLayer = new PDOLayer($config);
    if($pdoLayer->error === '') {
        echo PHP_EOL . ' conn is active ' . PHP_EOL;
        // Assign globaly
        $conn = $pdoLayer->conn;
    } else {
        echo PHP_EOL . ' conn NOT active: '  . $pdoLayer->error . PHP_EOL;
    }
    // print_r($conn);
}
else {
    echo PHP_EOL . ' conn NOT active. Create new! ' . PHP_EOL;
    // print_r($conn);
}


include_once __DIR__ . '/pdo/insert.php';
include_once __DIR__ . '/pdo/update.php';
include_once __DIR__ . '/pdo/delete.php';
include_once __DIR__ . '/pdo/selects.php';
include_once __DIR__ . '/pdo/join.php';
include_once __DIR__ . '/pdo/insertCustom.php';
