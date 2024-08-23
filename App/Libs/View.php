<?php

namespace App\Libs;

/*
* Cache
*/
class View
{
    // View Template
    public static function template($file, $data = [], $page = false)
    {
        $session_id = isset($data['current_user']->id) ? $data['current_user']->id : 0;
        if (!$session_id) {
            DB::run("INSERT INTO `guests` (`ip`, `time`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `time` = ?", [Ip::getIP(), gmtime(), gmtime()]);
        }

        extract($data);

        if ($page == 'user' && file_exists("../App/Views/user/$file.php")) {
            require_once "assets/themes/" . (isset($data['current_user']->stylesheet) ? $data['current_user']->stylesheet : CONFIG['DEFAULTTHEME']) . "/theme.php";
        } elseif ($page == 'admin' && file_exists("../App/Views/admin/$file.php")) {
            require_once CONFIG['APPROOT'] . "/Views/admin/admincp.php";
        } elseif (file_exists("../App/Views/user/shoutboxload.php")) {
            require_once CONFIG['APPROOT'] . "/Views/user/shoutboxload.php";
        } else {
            showMessage(Url(), "View $file does not exist");
        }
        return;
    }
    
}