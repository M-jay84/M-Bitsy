<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Ip;
use App\Libs\TTMail;
use App\Libs\Validate;
use App\Models\BansLogic;
use App\Models\MessagesLogic;
use App\Models\ShoutboxLogic;
use PDO;

/*
* Account Data
*/
class AccountsLogic
{
    // Login If User Allowed
    public static function login()
    {
        // Check Inputs
        Validate::captcha_response($_POST['g-recaptcha-response']);
        $username = Validate::Input('username');
        $password = Validate::Input('password');

        if (empty($username) || empty($password)) {
            showMessage(URLROOT, LANG["MISSING_FORM_DATA"]);
        }

        // Find User
        $users_query = "SELECT id, password, secret, status, enabled FROM `users` WHERE username=?"; 
        $sql = DB::run($users_query, [$username])->fetch(PDO::FETCH_ASSOC);
        // Run Some Checks
        if (!$sql || !password_verify($password, $sql['password'])) {
            showMessage('logout', LANG["LOGIN_INCORRECT"]);
        } elseif ($sql['status'] == "pending") {
            showMessage('logout', LANG["ACCOUNT_PENDING"]);
        } elseif ($sql['enabled'] == "no") {
            showMessage('logout', LANG["ACCOUNT_DISABLED"]);
        }

        // Set Cookies
        setcookie('id', $sql['id'], CONFIG['TT_TIME']+60*60*24*30 );
        setcookie('password', $sql['password'], CONFIG['TT_TIME']+60*60*24*30 );
        $users_query = "UPDATE users SET last_login=? WHERE id=?";
        DB::run($users_query, [CONFIG['TT_DATE'], $_SESSION['id']]);
        showMessage(URLROOT, LANG["SUCCESS"]);
    }

    // Check invite Signup
    public static function checkinvite()
    {
        // Check Invite
        $invite = (isset($_GET['invite'])) ? $_GET['invite'] : 0;
        $secret = (isset($_GET['secret'])) ? $_GET['invite'] : 0;
        $invite_row = 0;

        if (!intval($invite) || strlen($secret) != 20) {
            if (CONFIG['INVITEONLY']) {
                showMessage(URLROOT, "<center>" . LANG["INVITE_ONLY_MSG"] . "<br></center>");
            }
        } else {
            $invite_query = "SELECT id FROM `users` WHERE id = ? AND secret=?";
            $invite_row = DB::run($invite_query, [$invite,$secret])->fetch(PDO::FETCH_ASSOC);

            if (!$invite_row) {
                showMessage(URLROOT, LANG["INVITE_ONLY_NOT_FOUND"] . "" . (CONFIG['SIGNUPTIMEOUT'] / 86400) . "days.");
            }
        }
        return $invite_row;
    }

    // Update User pass & secret
    public static function signupUser()
    {
        // Check Input
        Validate::captcha_response($_POST['g-recaptcha-response']);
        $data = [
            'passagain' => Validate::Input('passagain'),
            'invite' => Validate::Input('invite', 'int'),
            'username' => Validate::Input('wantusername'),
            'email' => Validate::Input('email'),
            'password' => Validate::Input('wantpassword'),
            'country' => Validate::Input('country', 'int'),
            'gender' => Validate::Input('gender'),
            'client' => Validate::Input('client'),
            'age' => Validate::Input('age', 'int'),
            'secret' => Validate::Input('type'),
        ];
        
        // Check User Invite
        if (strlen($data['secret']) == 20 || !is_numeric($data['invite'])) {
            $users_query = "SELECT id FROM `users` WHERE id = ? AND secret=?";
            $invite_row1 = DB::run($users_query, [$data['invite'],$data['secret']])->fetch(PDO::FETCH_ASSOC);
            $invite_row = (int) $invite_row1['id'] ?? 0;
        }

        // Check User Input
        $message = "";

        if (empty($data['password']) || (empty($data['email']) && !$invite_row) || empty($data['username'])) {
            $message = LANG["DONT_LEAVE_ANY_FIELD_BLANK"];
        } elseif (strlen($data['username']) > 50) {
            $message = sprintf(LANG["USERNAME_TOO_LONG"], 16);
        } elseif ($data['password'] != $data['passagain']) {
            $message = LANG["PASSWORDS_NOT_MATCH"];
        } elseif (strlen($data['password']) < 6) {
            $message = sprintf(LANG["PASS_TOO_SHORT_2"], 6);
        } elseif (strlen($data['password']) > 16) {
            $message = sprintf(LANG["PASS_TOO_LONG_2"], 16);
        } elseif ($data['password'] == $data['username']) {
            $message = LANG["PASS_CANT_MATCH_USERNAME"];
        } elseif (!preg_match('/^[a-z0-9 .\-]+$/i', $data['username'])) {
            $message = LANG["INVALID_NAME"];
        } elseif ($data['invite_row'] = false && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $message = LANG["INVALID_EMAIL"];
        }

        if ($message == "") {
            // If NOT Invite Check
            if (!$invite_row) {
                // Check Max Users
                $users_query = "SELECT count(id) FROM `users`";
                $numsitemembers = DB::run($users_query)->fetchColumn();
                if ($numsitemembers >= CONFIG['MAXUSERS']) {
                    $msg = LANG["SITE_FULL_LIMIT_MSG"] . number_format(CONFIG['MAXUSERS']) . " " . LANG["SITE_FULL_LIMIT_REACHED_MSG"] . " " . number_format($numsitemembers) . " members";
                    showMessage(URLROOT, $msg);
                }

                // Check Email Not Banned
                $message = BansLogic::checkEmail($data['email']);
            }

            // Check Username NOT In Use
            $users_query = "SELECT id FROM `users` WHERE username=?";
            $count =DB::run($users_query, [$data['username']])->fetchColumn();
            if ($count != 0) {
                $message = sprintf(LANG["USERNAME_INUSE_S"], $data['username']);
            }

            // Generate Secret
            $data['secret'] = mksecret();

            // Hash Password
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            // Make Passkey
            $rand = array_sum(explode(" ", microtime()));
            $passkey = md5($data['username'] . $rand . $data['secret'] . ($rand * mt_rand()));
            $data['passkey'] = $passkey;
        }

        // Check Message Error
        if ($message != "") {
            showMessage('login', $message);
        }

        // No Message So Proceed
        if ($message == "") {
            // Invited User
            if ($invite_row) {
                $users_query = "UPDATE users SET username=? AND password=? AND class=? AND secret=? AND status=? AND added=? AND passkey=?
                 WHERE id=?";
                DB::run($users_query, [$data['username'], $data['password'], 1, $data['secret'], 'confirmed', CONFIG['TT_DATE'], $data['passkey'], $invite_row]
                );

                // Welcome PM
                MessagesLogic::newinsert(['sender' => 0, 'receiver' => $invite_row, 'added' => CONFIG['TT_DATE'], 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);

                // Post Shout
                $shoutbox_query = "INSERT INTO shoutbox (userid, date, user, message) VALUES (?, ?, ?, ?)";
                DB::run($shoutbox_query, [0, CONFIG['TT_DATE'], 'System', "new user: " . $data['username'] . " has joined."]);

                // Invite Activated
                showMessage('login', LANG["ACCOUNT_ACTIVATED"]);
            }

            // Check To Confirm
            if (CONFIG['CONFIRMEMAIL'] || CONFIG['ACONFIRM']) {
                $data['status'] = 'pending';
            } else {
                $data['status'] = 'confirmed';
            }

            // Set Class
            if ($numsitemembers == '0') {
                $data['class'] = '7';
            } else {
                $data['class'] = '1';
            }

            // Standard Inputs
            $data['added'] = CONFIG['TT_DATE'];
            $data['last_login'] = CONFIG['TT_DATE'];
            $data['last_access'] = CONFIG['TT_DATE'];
            $data['stylesheet'] = CONFIG['DEFAULTTHEME'];
            $data['language'] = CONFIG['DEFAULTLANG'];
            $data['ip'] = Ip::getIP();

            // Insert User
            $users_query = "INSERT INTO users (username,password,secret,email,status,added,last_login,last_access,age,country,gender,client, 
                 stylesheet,language,class,ip,passkey) VALUES (?, ?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            DB::run($users_query, [$data['username'], $data['password'], $data['secret'], $data['email'], 
                $data['status'], CONFIG['TT_DATE'], CONFIG['TT_DATE'], CONFIG['TT_DATE'],
                 $data['age'], $data['country'], $data['gender'], $data['client'], 
                 CONFIG['DEFAULTTHEME'], CONFIG['DEFAULTLANG'], $data['class'], Ip::getIP(), $data['passkey']]);

            $id = DB::lastInsertId();

            // Post Shout
            $shoutbox_query = "INSERT INTO shoutbox (userid, date, user, message) VALUES (?, ?, ?, ?)";
            DB::run($shoutbox_query, [0, CONFIG['TT_DATE'], 'System', "new user: " . $data['username'] . " has joined."]);
            // Welcome PM
            MessagesLogic::newinsert(['sender' => 0, 'receiver' => $id, 'added' => CONFIG['TT_DATE'], 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);

            // Admin Confirm
            if (CONFIG['ACONFIRM']) {
                $body = LANG["YOUR_ACCOUNT_AT"] . " " . CONFIG['SITENAME'] . " " . LANG["HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT"] . "\n\n" . CONFIG['SITENAME'] . " " . LANG["ADMIN"];
            } else {
                $body = LANG["YOUR_ACCOUNT_AT"] . " " . CONFIG['SITENAME'] . " " . LANG["HAS_BEEN_APPROVED_EMAIL"] . "\n\n	" . URLROOT . "/confirm/confirmemail/$id/$data[secret]\n\n" . LANG["HAS_BEEN_APPROVED_EMAIL_AFTER"] . "\n\n	" . LANG["HAS_BEEN_APPROVED_EMAIL_DELETED"] . "\n\n" . URLROOT . " " . LANG["ADMIN"];
            }

            // Confirm Account
            if (CONFIG['CONFIRMEMAIL'] || CONFIG['ACONFIRM']) {
                $TTMail = new TTMail();
                $TTMail->Send($data['email'], "Your " . CONFIG['SITENAME'] . " User Account", $body, "", "-f" . CONFIG['SITEEMAIL'] . "");
                showMessage('login', LANG["A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"] . " (" . htmlspecialchars($data['email']) . "). " . LANG["ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"] . " <br/ >");
            } else {
                showMessage('login', LANG["ACCOUNT_ACTIVATED"]);
            }
        }
    }

}