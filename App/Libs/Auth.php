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
    public $currentuser;
    
    // Check User/Visitor & Set $_SESSION
    public function __construct($autoclean = false)
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
                self::destroy_cookies();
                showMessage('logout', LANG['AUTH_ISSUE']);
            }

            // Site closeed
            self::isClosed($data['class']);

            // Now Compare Checks
            if ($data['password'] != $_COOKIE['password']) {
                showMessage('logout', true);
            }
            if ($data['id'] != $_COOKIE['id']) {
                showMessage('logout', true);
            }

            // User So Update & Set Session
            if ($data) {
                if ($data['passkey'] == '') {
                    $rand = array_sum(explode(" ", microtime()));
                    $passkey = md5($data['username'] . $rand . $data['secret'] . ($rand * mt_rand()));
                    DB::update('users', ['passkey','last_access','ip','page'], ['id='], [$passkey, CONFIG['TT_DATE'], Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                } else {
                    DB::update('users', ['last_access','ip','page'], ['id='], [CONFIG['TT_DATE'], Ip::getIP(), $_SERVER['REQUEST_URI'], $data["id"]]);
                }
                $_SESSION = $data;
                $_SESSION['loggedin'] = true;
                $data['loggedin'] = true;
                if ($data['class'] >= CONFIG['_MODERATOR']) {
                    $data['isStaff'] = true;
                }
                $this->currentuser = $data;
                unset($data);
            }

            // Run Cleanup
            if ($autoclean) {
                Cleanup::autoClean();
            }

            return $this->currentuser;
        }
    }

    // Get User Value
    public function __get($var){
        if ($this->currentuser[$var]) {
            return $this->currentuser[$var];
        } else {
            return 0;
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

    // Check Site Closed
    public static function isClosed($class = false)
    {
        if (!CONFIG['SITE_ONLINE']) {
            if ($class < CONFIG['_MODERATOR']) {
                ob_start();
                ob_clean();
                require_once VIEW_PATH . "/inc/header.php";
                echo '<div class="alert ttalert"><center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center></div>';
                require_once VIEW_PATH . "/inc/footer.php";
                die();
            } else {
                echo '<center>' . stripslashes(CONFIG['OFFLINEMSG']) . '</center>';
            }
        }
    }

    // Log User Out
    public static function destroy_cookies()
    {
        setcookie('id', '', CONFIG['TT_TIME']-45);
        setcookie('password', '', CONFIG['TT_TIME']-45);
        setcookie('PHPSESSID', '', CONFIG['TT_TIME']-45);
        $_SESSION = array();
        unset($_SESSION);
        @session_destroy();
    }

}