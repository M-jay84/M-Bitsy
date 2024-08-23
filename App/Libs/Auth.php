<?php

namespace App\Libs;

use App\Libs\Ip;
use App\Libs\DB;

/*
* Check Visitor
*/
class Auth
{
    public $data = array();
    
    // Check User/Visitor & Set $_SESSION
    public function user($force = 0, $autoclean = false)
    {
        // Check Ip
        self::ipBanned();

        $cookie_id = isset($_COOKIE["id"]) ? $_COOKIE["id"] : '';
        $cookie_password = isset($_COOKIE["password"]) ? $_COOKIE["password"] : '';
        $session_class = isset($_SESSION['class']) ? $_SESSION['class'] : 0;

        // Check Cookies
        if (strlen($cookie_password) != 60 || !is_numeric($cookie_id)) {
            $_SESSION["id"] = 0;
            $_SESSION['class'] = 0;
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
                $permission = DB::fetchRow('permissions', '*', ['permission_id ='], [$role]);
                foreach ($permission as $key => $value) {
                    $data[$key] = $value;
                }
            } catch (\Exception $e) {
                destroy_cookies();
                showMessage(Url('logout'), LANG['AUTH_ISSUE']);
            }

            // Site closeed
            self::isClosed($data['class']);

            // Now Compare Checks
            if ($data['password'] != $_COOKIE['password']) {
                showMessage(Url('logout'), true);
            }
            if ($data['id'] != $_COOKIE['id']) {
                showMessage(Url('logout'), true);
            }

            // User So Update & Set Session
            if ($data) {
                if ($data['passkey'] == '') {
                    $rand = array_sum(explode(" ", microtime()));
                    $passkey = md5($data['username'] . $rand . $data['secret'] . ($rand * mt_rand()));
                    DB::update('users', ['passkey','last_access','ip','page'], ['id='], [$passkey, get_date_time(), Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                } else {
                    DB::update('users', ['last_access','ip','page'], ['id='], [get_date_time(), Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                }
				$_SESSION = $data;
                $_SESSION['loggedin'] = true;
                $data['loggedin'] = true;
                if ($data['class'] >= CONFIG['_MODERATOR']) {
                    $data['isStaff'] = true;
                }
                //$this->data = $data;
                //unset($data);
            }

            // Run Cleanup
            if ($autoclean) {
                Cleanup::autoClean();
            }

            return (object) $data;
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
                showMessage(Url('logout'), true);
            }
        } elseif ($force == 2) {
            if (!$_SESSION['id']) {
                showMessage(Url('login'), true);
            }
        }
    }

    // Check Site Closed
    public static function isClosed($class = false)
    {
        if (!CONFIG['SITE_ONLINE']) {
            if ($class < CONFIG['_MODERATOR']) {
                ob_start();
                ob_clean();
                require_once "assets/themes/" . ($_SESSION['stylesheet'] ?: CONFIG['DEFAULTTHEME']) . "/header.php";
                echo '<div class="alert ttalert"><center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center></div>';
                require_once "assets/themes/" . ($_SESSION['stylesheet'] ?: CONFIG['DEFAULTTHEME']) . "/footer.php";
                die();
            } else {
                echo '<center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center>';
            }
        }
    }

}