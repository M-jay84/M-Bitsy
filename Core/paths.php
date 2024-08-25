<?php
// Autoload Classes By Nmanespace
spl_autoload_register(function ($className) {
    $filename = '../' . DIRECTORY_SEPARATOR . str_replace('\\', '/', $className) . '.php';
    if (file_exists($filename)) {
        require_once($filename);
    }
});