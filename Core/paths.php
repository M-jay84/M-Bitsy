<?php

// DB Details
define('DB_HOST', 'localhost');
define('DB_USER', 'dbname');
define('DB_PASS', 'dbpassword');
define('DB_NAME', 'dbname1');
define('DB_CHAR', 'utf8mb4');
// URL Root
define('URLROOT', 'http://localhost/M-BiTsy');
// App Root
define('APPROOT', '../');
// Paths
define('CACHE_PATH', '../Core/files/cache');
define('LOG_PATH', '../Core/files/logs');
define('UPLOAD_PATH', '../Core/files');
define('VIEW_PATH', '../App/Views');

// Autoload Classes By Nmanespace
spl_autoload_register(function ($className) {
    $filename = APPROOT . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
    if (file_exists($filename)) {
        require_once($filename);
    }
});