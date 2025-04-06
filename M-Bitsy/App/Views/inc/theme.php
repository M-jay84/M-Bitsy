<?php

define('DEBUG', false); // NEVER ON LIVE SITE

require VIEW_PATH . "/inc/header.php";
if (DEBUG) {
    echo '<pre>'.print_r($data, true).'</pre>';
}
require VIEW_PATH . "/$page/$file.php";
require VIEW_PATH . "/inc/footer.php";