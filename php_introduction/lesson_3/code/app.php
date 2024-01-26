<?php
require_once __DIR__ . '/vendor/autoload.php';

// вызов корневой функции
$result = main(__DIR__ . "/config.ini");
// вывод результата
echo $result;