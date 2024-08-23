<?php

// Get Config/Paths
require('../Core/init.php');

// Autoload Classes
spl_autoload_register(function ($className) {
    $filename = CONFIG['NAME_SPACE'] . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
    if (file_exists($filename)) {
        require_once($filename);
    }
});

// Run App
use App\Libs\Router;
new Router();