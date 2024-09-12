<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Ip;
use App\Libs\Validate;
use App\Models\MessagesModel;
use App\Models\BansModel;
use App\Models\UsersModel;
use App\Models\ShoutboxModel;

/*
* Account Data
*/
class AccountsModel
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
        $sql = DB::fetchRow('users', 'id, password, secret, status, enabled', ['username ='], [$username]);

        // Run Some Checks
        if (!$sql || !password_verify($password, $sql['password'])) {
            showMessage(('logout'), LANG["LOGIN_INCORRECT"]);
        } elseif ($sql['status'] == "pending") {
            showMessage(('logout'), LANG["ACCOUNT_PENDING"]);
        } elseif ($sql['enabled'] == "no") {
            showMessage(('logout'), LANG["ACCOUNT_DISABLED"]);
        }

        // Set Cookies
        setcookie('id', $sql['id'], CONFIG['TT_TIME']+60*60*24*30 );
        setcookie('password', $sql['password'], CONFIG['TT_TIME']+60*60*24*30 );
        // Update User
        DB::update('users', ['last_login'], ['id='], [CONFIG['TT_DATE'], $sql['id']]);
        showMessage(URLROOT, LANG["SUCCESS"]);
    }

    // Check invite Signup
    public static function checkinvite() {
        // Check Invite
        $invite = (isset($_GET['invite'])) ? $_GET['invite'] : 0;
        $secret = (isset($_GET['secret'])) ? $_GET['invite'] : 0;
        $invite_row = 0;

        if (!intval($invite) || strlen($secret) != 20) {
            if (CONFIG['INVITEONLY']) {
                showMessage(URLROOT, "<center>" . LANG["INVITE_ONLY_MSG"] . "<br></center>");
            }
        } else {
            $invite_row = DB::fetchRow('users', 'id', ['id =', 'secret ='], [$invite,$secret]);
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
            $invite_row1 = DB::fetchRow('users', 'id', ['id =', 'secret ='], [$data['invite'],$data['secret']]);
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
                $numsitemembers = DB::fetchColumn('users', 'count(id)', '');
                if ($numsitemembers >= CONFIG['MAXUSERS']) {
                    $msg = LANG["SITE_FULL_LIMIT_MSG"] . number_format(CONFIG['MAXUSERS']) . " " . LANG["SITE_FULL_LIMIT_REACHED_MSG"] . " " . number_format($numsitemembers) . " members";
                    showMessage(URLROOT, $msg);
                }

                // Check Email Not Banned
                $message = BansModel::checkEmail($data['email']);
            }

            // Check Username NOT In Use
            $count = DB::fetchColumn('users', 'COUNT(id)', ['username ='], [$data['username']]);
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
            showMessage(('login'), $message);
        }

        // No Message So Proceed
        if ($message == "") {
            // Invited User
            if ($invite_row) {
                DB::update('users', ['username', 'password', 'class', 'secret', 'status', 'added', 'passkey'], ['id='],
                    [$data['username'], $data['password'], 1, $data['secret'], 'confirmed', CONFIG['TT_DATE'], $data['passkey'], $invite_row]
                );

                // Welcome PM
                MessagesModel::insert(['sender' => 0, 'receiver' => $invite_row, 'added' => CONFIG['TT_DATE'], 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);

                // Post Shout
                ShoutboxModel::insert(['userid' => 0, 'date' => CONFIG['TT_DATE'], 'user' => 'System', 'message' => "new user: " . $data['username'] . " has joined."]);
                // Invite Activated
                showMessage(('login'), LANG["ACCOUNT_ACTIVATED"]);
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
            DB::insert('users', 
                ['username'=>$data['username'], 'password'=>$data['password'], 'secret'=>$data['secret'], 'email'=>$data['email'], 
                'status'=>$data['status'], 'added'=>CONFIG['TT_DATE'], 'last_login'=>CONFIG['TT_DATE'], 'last_access'=>CONFIG['TT_DATE'],
                 'age'=>$data['age'], 'country'=>$data['country'], 'gender'=>$data['gender'], 'client'=>$data['client'], 
                 'stylesheet'=>CONFIG['DEFAULTTHEME'], 'language'=>CONFIG['DEFAULTLANG'], 'class'=>$data['class'], 'ip'=>Ip::getIP(), 'passkey'=>$data['passkey']]
            );

            $id = DB::lastInsertId();

            // Post Shout
            ShoutboxModel::insert(['userid' => 0, 'date' => CONFIG['TT_DATE'], 'user' => 'System', 'message' => "new user: " . $data['username'] . " has joined."]);

            // Welcome PM
            MessagesModel::insert(['sender' => 0, 'receiver' => $id, 'added' => CONFIG['TT_DATE'], 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);

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
                showMessage(('login'), LANG["A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"] . " (" . htmlspecialchars($data['email']) . "). " . LANG["ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"] . " <br/ >");
            } else {
                showMessage(('login'), LANG["ACCOUNT_ACTIVATED"]);
            }
        }
    }

}