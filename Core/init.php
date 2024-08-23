<?php
// Start Session
session_start();

// Load Config
require 'config.php';

// Register custom exception handler
include "Helpers/error_helper.php";
error_handler(); //todo
set_exception_handler("handleUncaughtException");

// Load Langauge
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'english';
require CONFIG['LANG']."/$language.php";

// Load Main Functions
require "Helpers/my_helper.php";

// Load Helpers
require "Helpers/session_helper.php";
require "Helpers/bbcode_helper.php";
require "Helpers/datetime_helper.php";
require "Helpers/displaytable_helper.php";
require "Helpers/format_helper.php";
require "Helpers/forum_helper.php";
require "Helpers/other_helper.php";
require "Helpers/torrent_helper.php";
require "Helpers/validate_helper.php";