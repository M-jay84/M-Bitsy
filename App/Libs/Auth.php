<?php

namespace App\Libs;

use App\Libs\Ip;
use App\Models\PermissionsModel;
use App\Models\UsersModel;

/*
* Check Visitor
*/
class Auth
{
    // Check User/Visitor & Set $_SESSION
    public static function user($class = 0, $force = 0, $autoclean = false)
    {
        // Check Ip
        self::ipBanned();

        $cookie_id = isset($_COOKIE["id"]) ? $_COOKIE["id"] : '';
        $cookie_password = isset($_COOKIE["password"]) ? $_COOKIE["password"] : '';
        $session_class = isset($_SESSION['class']) ? $_SESSION['class'] : 0;

        // Check Cookies
        if (strlen($cookie_password) != 60 || !is_numeric($cookie_id)) {
            self::isClosed($session_class);
            self::isLoggedIn($force);
            return;
        } else {
            // Is User A Member
            try {
                // Lets Build User Array
                $data = array();
                // Get User & Group
                $user = DB::run(
                    "SELECT * 
                    FROM `users` 
                    LEFT OUTER JOIN `groups` 
                    ON users.class=groups.group_id 
                    WHERE id = $cookie_id 
                    AND users.enabled='yes'
                    AND users.status ='confirmed'"
                )->fetch();
                foreach ($user as $key => $value) {
                    $data[$key] = $value;
                }
                // User Role Or Group Role
                $role = $user['role_id'] == 1 ? $user['group_role_id'] : $user['role_id'];
                // Now Get Permission
                $permission = PermissionsModel::fetchRow('*', ['permission_id ='], [$role]);
                foreach ($permission as $key => $value) {
                    $data[$key] = $value;
                }
            } catch (\Exception $e) {
                destroy_cookies();
                Redirect(Url('logout'), Txt('AUTH_ISSUE'));
            }

            // Site closeed
            self::isClosed($data['class']);

            // Now Compare Checks
            if ($data['password'] != $_COOKIE['password']) {
                Redirect(Url('logout'));
            }
            if ($data['id'] != $_COOKIE['id']) {
                Redirect(Url('logout'));
            }
            if ($class != 0 && $class > $data['class']) {
                Redirect(Url(), Txt("NO_PERMISSION"));
            }

            // User So Update & Set Session
            if ($data) {
                if ($data['passkey'] == '') {
                    $rand = array_sum(explode(" ", microtime()));
                    $passkey = md5($data['username'] . $rand . $data['secret'] . ($rand * mt_rand()));
                    UsersModel::update(['passkey','last_access','ip','page'], ['id='], [$passkey, get_date_time(), Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                } else {
                    UsersModel::update(['last_access','ip','page'], ['id='], [get_date_time(), Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                }
				$GLOBALS["CURUSER"] = $data;
                $_SESSION['stylesheet'] = $data['stylesheet'];
                $_SESSION['language'] = $data['language'];
                $_SESSION['tzoffset'] = $data['tzoffset'];
                $_SESSION['id'] = $data['id'];
                $_SESSION['class'] = $data['class'];
                $_SESSION['privacy'] = $data['privacy'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['avatar'] = $data['avatar'];
                unset($data);
            }

            // Run Cleanup
            if ($autoclean) {
                Cleanup::autoClean();
            }
        }
    }

    // Check Ip Banned
    public static function ipBanned()
    {
        $ip = Ip::getIP();
        if ($ip == '') {
            return;
        }
        Ip::checkIpBan($ip);
    }

    // Check Logged In Level
    public static function isLoggedIn($force = 0)
    {
        // If force 0 guest view, force 1 use config CONFIG['MEMBERSONLY'], force 2 always hidden from guest
        if ($force == 1 && CONFIG['MEMBERSONLY']) {
            if (!$_SESSION['id']) {
                Redirect(Url('logout'));
            }
        } elseif ($force == 2) {
            if (!$_SESSION['id']) {
                Redirect(Url('login'));
            }
        }
    }

    // Check Site Closed
    public static function isClosed($class = false)
    {
        if (!CONFIG['SITE_ONLINE']) {
            if ($class < _MODERATOR) {
                ob_start();
                ob_clean();
                require_once "assets/themes/" . ($USER->has('stylesheet') ?: CONFIG['DEFAULTTHEME']) . "/header.php";
                echo '<div class="alert ttalert"><center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center></div>';
                require_once "assets/themes/" . ($USER->has('stylesheet') ?: CONFIG['DEFAULTTHEME']) . "/footer.php";
                die();
            } else {
                echo '<center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center>';
            }
        }
    }

}