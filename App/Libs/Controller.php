<?php

namespace App\Libs;

/*
* Base Controller
*/
class Controller
{
    // Display View Template
    public function template($file, $data = [], $page = false)
    {
        //$session_id = isset($data['current_user']->id) ? $data['current_user']->id : 0;
        //if (!$session_id) {
        //$guests_query = "INSERT INTO `guests` (`ip`, `time`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `time` = ?";
        //    DB::run($guests_query, [Ip::getIP(), CONFIG['TT_TIME'], CONFIG['TT_TIME']]);
        //}

        extract($data);

        $TTCache = new Cache();

        if ($page == 'user' && file_exists("../App/Views/user/$file.php")) {
            $default_theme = (isset($_SESSION['stylesheet']) && $_SESSION['stylesheet']) != '' ? $_SESSION['stylesheet'] : 'inc';
            require_once VIEW_PATH . "/$default_theme/theme.php";
        } elseif ($page == 'admin' && file_exists("../App/Views/admin/$file.php")) {
            require_once VIEW_PATH . "/admin/admin-cp.php";
        } elseif (file_exists("../App/Views/user/user-shoutboxload.php")) {
            require_once VIEW_PATH . "/user/user-shoutboxload.php";
        } else {
            showMessage(URLROOT, "View $file does not exist");
        }
        return;
    }

    // Redirect
    public function redirect($url)
    {
        if ($url != URLROOT) {
            $url = URLROOT . '/' . $url;
        } else {
            $url = URLROOT;
        }
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="3; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    }
}