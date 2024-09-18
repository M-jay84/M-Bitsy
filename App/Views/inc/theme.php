<?php

define('DEBUG', false); // NEVER ON LIVE SITE

require VIEW_PATH . "/inc/header.php";
if (DEBUG && $_SESSION['class'] == 7 && $_SESSION['id'] == 1 && $_GET['url'] == '') {
    echo '<pre>'.print_r($data, true).'</pre>';
    echo '<pre>'.print_r($_GET, true).'</pre>';
    echo '<pre>'.print_r($_POST, true).'</pre>';
    echo '<pre>'.print_r($_FILES, true).'</pre>';
}
require VIEW_PATH . "/$page/$file.php";
require VIEW_PATH . "/inc/footer.php";