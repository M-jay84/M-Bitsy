<?php
use App\Libs\Errors;

// Start Session
session_start();

// Load Paths / Autoload
require 'paths.php';

// Load Config Array
require 'config.php';

// Handle Errors/Exceptions
(new Errors)->error_handler(true);

// Load Langauge Array
require 'language.php';

// Load Functions
require 'functions.php';