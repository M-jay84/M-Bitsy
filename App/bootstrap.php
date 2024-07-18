<?php
// Start Session
session_start();

// Error Reporting
error_reporting(E_ALL); // error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//ini_set('error_log', '../data/logs/errors_log.txt');

// Register custom exception handler
include "Helpers/exception_helper.php";
set_exception_handler("handleUncaughtException");
//set_error_handler('runtime_error_handler');

// Load Config
require 'Config/config.php';
require 'Config/settings.php';
// Load Main Functions
require "Helpers/globals_helper.php";
// Load Langauge
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'english';
require LANG."/$language.php";
// Load Helpers
require "Helpers/bbcode_helper.php";
require "Helpers/datetime_helper.php";
require "Helpers/displaytable_helper.php";
require "Helpers/format_helper.php";
require "Helpers/forum_helper.php";
require "Helpers/other_helper.php";
require "Helpers/torrent_helper.php";
require "Helpers/validate_helper.php";