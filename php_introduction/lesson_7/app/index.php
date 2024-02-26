<?php

use Root\App\App;

require_once(__DIR__ . '/vendor/autoload.php');

echo (new App())->run();