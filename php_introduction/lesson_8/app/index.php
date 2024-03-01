<?php

use Root\App\Services\Render;

try {
    require_once(__DIR__ . '/vendor/autoload.php');
    echo \App::app()->run();
} catch (Throwable $e) {
    // TODO после выноса рендера, вызывать его
    var_dump($e);
}