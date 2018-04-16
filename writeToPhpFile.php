<?php

// Write PHP content
// $filename = "config/dbLayer.php";
$phpContent = "<?php";
$phpContent .= "  // " . $filename . PHP_EOL.PHP_EOL;

$phpContent .= "/*".PHP_EOL;
$phpContent .= "|--------------------------------------------------------------------------".PHP_EOL;
$phpContent .= "| DATABASE configuration".PHP_EOL;
$phpContent .= "| Change credentials".PHP_EOL;
$phpContent .= "|--------------------------------------------------------------------------".PHP_EOL;
$phpContent .= "*/".PHP_EOL.PHP_EOL;
$phpContent .= 'return $config = ['.PHP_EOL;
foreach ($config as $key => $value) {
    $phpContent .= "\t\t'".$key ."' => '" . $value. "'," . PHP_EOL;
}
$phpContent .= '];'. PHP_EOL;


file_put_contents(getcwd(). $filename, $phpContent);
if(!file_exists(getcwd().$filename)) {
    echo $filename . ' DOES not EXIST: ' . getcwd().$filename;
}