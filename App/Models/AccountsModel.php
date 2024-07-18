<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Ip;
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
        captcha_response($_POST['g-recaptcha-response']);
        $username = Input('username');
        $password = Input('password');
        if (empty($username) || empty($password)) {
            Redirect(Url(), Txt("MISSING_FORM_DATA"));
        }
        // Find User
        $sql = UsersModel::fetchRow('id, password, secret, status, enabled', ['username ='], [$username]);

        // Run Some Checks
        if (!$sql || !password_verify($password, $sql['password'])) {
            Redirect(Url('logout'), Txt("LOGIN_INCORRECT"));
        } elseif ($sql['status'] == "pending") {
            Redirect(Url('logout'), Txt("ACCOUNT_PENDING"));
        } elseif ($sql['enabled'] == "no") {
            Redirect(Url('logout'), Txt("ACCOUNT_DISABLED"));
        }

        // Set Cookies
        set_cookies($sql['id'], $sql['password']);

        // Update User
        UsersModel::update(['last_login'], ['id='], [get_date_time(), $sql['id']]);
        Redirect(Url(), Txt("SUCCESS"));
    }

    // Recover Password
    public static function recoverPassword()
    {
        // Check Inputs
		$email = Input('email', 'email');

        captcha_response($_POST['g-recaptcha-response']);

        // Get User By Email
        $arr = UsersModel::fetchRow('id, username, email', ['email ='], [$email]);
        if (!$arr) {
            Redirect(Url(), Txt("EMAIL_ADDRESS_NOT_FOUND"));
        }

        // Send Email
        if ($arr) {
            $sec = mksecret();
            $id = $arr['id'];
            $username = $arr['username']; // 06/01
            $emailmain = CONFIG['SITEEMAIL'];
            $url = URLROOT;
            $body = Txt("SOMEONE_FROM") . " " . $_SERVER["REMOTE_ADDR"] . " " . Txt("MAILED_BACK") . " ($email) " . Txt("BE_MAILED_BACK") . " \r\n\r\n " . Txt("ACCOUNT_INFO") . " \r\n\r\n " . Txt("USERNAME") . ": " . $username . " \r\n " . Txt("CHANGE_PSW") . "\n\n$url/recover/confirm/$id/$sec\n\n\n" . $url . "\r\n";
            $TTMail = new TTMail();
            $TTMail->Send($email, Txt("ACCOUNT_DETAILS"), $body, "", "-f$emailmain");
            UsersModel::update(['secret'], ['email='], [$sec, $email], 1);
            Redirect(Url(), sprintf(Txt('MAIL_RECOVER'), htmlspecialchars($email)));
        }

        Redirect(Url(), Txt("NOTHING_FOUND"));
    }

    // Confirm Recover Password
    public static function changePassword($id, $secret)
    {
        // Check Inputs
        captcha_response($_POST['g-recaptcha-response']);
        $password = Input('password');
        $password1 = Input('password1');
        if (empty($password) || empty($password1)) {
             Redirect(Url(), Txt("MISSING_FORM_DATA"));
        } elseif ($password != $password1) {
            Redirect(Url(), Txt("PASSWORD_NO_MATCH"));
        }
        // Check User
        $count = UsersModel::fetchColumn('COUNT(id)', ['id =', 'secret ='], [$id,$secret]);
        if ($count != 1) {
            Redirect(Url(), Txt("NO_SUCH_USER"));
        }

        // Update User
        $newsec = mksecret();
        $wantpassword = password_hash($password, PASSWORD_BCRYPT);
        UsersModel::update(['password', 'secret'], ['id=', 'secret='], [$wantpassword, $newsec, $id, $secret]);

        Redirect(Url(), Txt("PASSWORD_CHANGED_OK"));
    }

    // Account Add Avatar
    public static function addavatar($id)
    {
        $maxsize = 130 * 130;
        $file = $_FILES['upfile']['name'];
        $ext = substr($file, -3);
        $allowedfiles = array("gif","jpg","png");
        $username = strtolower($_SESSION['username']);

        if($_FILES['upfile']['size'] > $maxsize) {
            Redirect(Url("profile/$id"), Txt("INVAILD_FILE_SIZE_IMAGE"));
        }

        if (in_array($ext, $allowedfiles)) {
            move_uploaded_file($_FILES['upfile']['tmp_name'], "uploads/avatars/$username.$ext");
            $avatar = URLROOT . "/uploads/avatars/$username.$ext";
            UsersModel::update(['avatar'], ['id='], [$avatar, $id]);
            Redirect(Url("profile/$id"), Txt("UP_AVATAR") . " OK");
        }
    }

    // Account password change
    public static function submitPassword($id)
    {
        $chpassword = Input('chpassword');
        $passagain = Input('passagain');
        if ((!$chpassword) || (!$passagain)) {
            Redirect(Url("profile/$id"), Txt("MISSING_FORM_DATA"));
        } elseif (strlen($chpassword) < 6) {
            Redirect(Url("profile/$id"), Txt("PASSWORD_SHORT"));
        } elseif ($chpassword != $passagain) {
            Redirect(Url("profile/$id"), Txt("PASSWORDS_NOT_MATCH"));
        }
        $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
        $secret = mksecret();
        $in = UsersModel::update(['password', 'secret'], ['id='], [$chpassword,$secret,$id]);
        if ($in > 0) {
            Redirect(Url('logout'), Txt("PASSWORD_CHANGED_OK"));
        } else {
            Redirect(Url("profile/$id"), Txt("ACTION_FAILED"));
        }
    }

    // Account password change
    public static function changeemail($id)
    {
        $email = Input('email', 'email');

        // Set Secret
        $sec = mksecret();
        $obemail = rawurlencode($email);
        $sitename = URLROOT;

        // Send Email
        $body = file_get_contents(EMAILDIR . "/changeemail.php");
        $body = str_replace("%usersname%", $_SESSION['username'], $body);
        $body = str_replace("%sitename%", $sitename, $body);
        $body = str_replace("%usersip%", $_SERVER["REMOTE_ADDR"], $body);
        $body = str_replace("%usersid%", $_SESSION['id'], $body);
        $body = str_replace("%userssecret%", $sec, $body);
        $body = str_replace("%obemail%", $obemail, $body);
        $body = str_replace("%newemail%", $email, $body);
        $TTMail = new TTMail();
        $TTMail->Send($email, "".CONFIG['SITENAME']." profile update confirmation", $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");

        // Update User Secret
        UsersModel::update(['editsecret'], ['id='], [$sec, $_SESSION['id']]);
        Redirect(Url("profile/$id"), Txt("EMAIL_CHANGE_SEND"));
    }

    // Check invite Signup
    public static function checkinvite() {
        // Check Invite
        $invite = (isset($_GET['invite'])) ? $_GET['invite'] : 0;
        $secret = (isset($_GET['secret'])) ? $_GET['invite'] : 0;
        $invite_row = 0;

        if (!Id($invite) || strlen($secret) != 20) {
            if (CONFIG['INVITEONLY']) {
                Redirect(Url(), "<center>" . Txt("INVITE_ONLY_MSG") . "<br></center>");
            }
        } else {
            $invite_row = UsersModel::fetchRow('id', ['id =', 'secret ='], [$invite,$secret]);
            if (!$invite_row) {
                Redirect(Url(), Txt("INVITE_ONLY_NOT_FOUND") . "" . (CONFIG['SIGNUPTIMEOUT'] / 86400) . "days.");
            }
        }
        return $invite_row;
    }

    // Update User pass & secret
    public static function signupUser()
    {
        // Check Input
        captcha_response($_POST['g-recaptcha-response']);
        $data = [
            'passagain' => Input('passagain'),
            'invite' => Input('invite', 'int'),
            'username' => Input('wantusername'),
            'email' => Input('email'),
            'password' => Input('wantpassword'),
            'country' => Input('country', 'int'),
            'gender' => Input('gender'),
            'client' => Input('client'),
            'age' => Input('age', 'int'),
            'secret' => Input('type'),
        ];
        
        // Check User Invite
        if (strlen($data['secret']) == 20 || !is_numeric($data['invite'])) {
            $invite_row1 = UsersModel::fetchRow('id', ['id =', 'secret ='], [$data['invite'],$data['secret']]);
            $invite_row = (int) $invite_row1['id'] ?? 0;
        }

        // Check User Input
        $message = "";

        if (empty($data['password']) || (empty($data['email']) && !$invite_row) || empty($data['username'])) {
            $message = Txt("DONT_LEAVE_ANY_FIELD_BLANK");
        } elseif (strlen($data['username']) > 50) {
            $message = sprintf(Txt("USERNAME_TOO_LONG"), 16);
        } elseif ($data['password'] != $data['passagain']) {
            $message = Txt("PASSWORDS_NOT_MATCH");
        } elseif (strlen($data['password']) < 6) {
            $message = sprintf(Txt("PASS_TOO_SHORT_2"), 6);
        } elseif (strlen($data['password']) > 16) {
            $message = sprintf(Txt("PASS_TOO_LONG_2"), 16);
        } elseif ($data['password'] == $data['username']) {
            $message = Txt("PASS_CANT_MATCH_USERNAME");
        } elseif (!username($data['username'])) {
            $message = Txt("INVALID_NAME");
        } elseif ($data['invite_row'] = false && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $message = Txt("INVALID_EMAIL");
        }

        if ($message == "") {
            // If NOT Invite Check
            if (!$invite_row) {
                // Check Max Users
                $numsitemembers = UsersModel::fetchColumn('count(id)', '');
                if ($numsitemembers >= CONFIG['MAXUSERS']) {
                    $msg = Txt("SITE_FULL_LIMIT_MSG") . number_format(CONFIG['MAXUSERS']) . " " . Txt("SITE_FULL_LIMIT_REACHED_MSG") . " " . number_format($numsitemembers) . " members";
                    Redirect(Url(), $msg);
                }

                // Check Email Not Banned
                $message = BansModel::checkEmail($data['email']);
            }

            // Check Username NOT In Use
            $count = UsersModel::fetchColumn('COUNT(id)', ['username ='], [$data['username']]);
            if ($count != 0) {
                $message = sprintf(Txt("USERNAME_INUSE_S"), $data['username']);
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
            Redirect(Url('login'), $message);
        }

        // No Message So Proceed
        if ($message == "") {
            // Invited User
            if ($invite_row) {
                UsersModel::update(['username', 'password', 'class', 'secret', 'status', 'added', 'passkey'], ['id='],
                    [$data['username'], $data['password'], 1, $data['secret'], 'confirmed', get_date_time(), $data['passkey'], $invite_row]
                );
                //var_dump($test); die();
                //UsersModel::update( ['username'=>$data['username'], 'password'=>$data['password'], 'class'=>1, 'secret'=>$data['secret'], 'status'=>'confirmed', 'added'=>get_date_time()], ['id'=>1]);
                // Welcome PM
                if (CONFIG['WELCOMEPM_ON']) {
                    MessagesModel::insert(['sender' => 0, 'receiver' => $invite_row, 'added' => get_date_time(), 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);
                }
                // Post Shout
                ShoutboxModel::insert(['userid' => 0, 'date' => get_date_time(), 'user' => 'System', 'message' => "new user: " . $data['username'] . " has joined."]);
                // Invite Activated
                Redirect(Url('login'), Txt("ACCOUNT_ACTIVATED"));
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
            $data['added'] = get_date_time();
            $data['last_login'] = get_date_time();
            $data['last_access'] = get_date_time();
            $data['stylesheet'] = CONFIG['DEFAULTTHEME'];
            $data['language'] = CONFIG['DEFAULTLANG'];
            $data['ip'] = Ip::getIP();

            // Insert User 
            UsersModel::insert(
                ['username'=>$data['username'], 'password'=>$data['password'], 'secret'=>$data['secret'], 'email'=>$data['email'], 
                'status'=>$data['status'], 'added'=>get_date_time(), 'last_login'=>get_date_time(), 'last_access'=>get_date_time(),
                 'age'=>$data['age'], 'country'=>$data['country'], 'gender'=>$data['gender'], 'client'=>$data['client'], 
                 'stylesheet'=>CONFIG['DEFAULTTHEME'], 'language'=>CONFIG['DEFAULTLANG'], 'class'=>$data['class'], 'ip'=>Ip::getIP(), 'passkey'=>$data['passkey']]
            );

            $id = DB::lastInsertId();

            // Post Shout
            ShoutboxModel::insert(['userid' => 0, 'date' => get_date_time(), 'user' => 'System', 'message' => "new user: " . $data['username'] . " has joined."]);

            // Welcome PM
            if (CONFIG['WELCOMEPM_ON']) {
                MessagesModel::insert(['sender' => 0, 'receiver' => $id, 'added' => get_date_time(), 'subject' => 'Welcome', 'msg' => CONFIG['WELCOMEPM_MSG'], 'unread' => 'yes', 'location' => 'in']);
            }

            // Admin Confirm
            if (CONFIG['ACONFIRM']) {
                $body = Txt("YOUR_ACCOUNT_AT") . " " . CONFIG['SITENAME'] . " " . Txt("HAS_BEEN_CREATED_YOU_WILL_HAVE_TO_WAIT") . "\n\n" . CONFIG['SITENAME'] . " " . Txt("ADMIN");
            } else {
                $body = Txt("YOUR_ACCOUNT_AT") . " " . CONFIG['SITENAME'] . " " . Txt("HAS_BEEN_APPROVED_EMAIL") . "\n\n	" . URLROOT . "/confirm/confirmemail/$id/$data[secret]\n\n" . Txt("HAS_BEEN_APPROVED_EMAIL_AFTER") . "\n\n	" . Txt("HAS_BEEN_APPROVED_EMAIL_DELETED") . "\n\n" . URLROOT . " " . Txt("ADMIN");
            }

            // Confirm Account
            if (CONFIG['CONFIRMEMAIL'] || CONFIG['ACONFIRM']) {
                $TTMail = new TTMail();
                $TTMail->Send($data['email'], "Your " . CONFIG['SITENAME'] . " User Account", $body, "", "-f" . CONFIG['SITEEMAIL'] . "");
                Redirect(Url('login'), Txt("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($data['email']) . "). " . Txt("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
            } else {
                Redirect(Url('login'), Txt("ACCOUNT_ACTIVATED"));
            }
        }
    }

    // Email Reset Confirm
    public static function confirmpassword($id, $secret, $email)
    {
        $editsecret = UsersModel::fetchColumn('editsecret', ['enabled =', 'status =', 'editsecret =', 'id ='], ['yes', 'confirmed', $secret, $id]);
       
        if (!$editsecret) {
            Redirect(Url(),  Txt("NOTHING_FOUND"));
        }

        if ($secret != $editsecret) {
            Redirect(Url(),  Txt("NOTHING_FOUND"));
        }

        // Update User Editsecret
        UsersModel::update(['editsecret','email'], ['id=', 'editsecret='], ['', $email, $id, $editsecret]);
        Redirect(Url(), Txt("SUCCESS"));
    }

    // Signup Email Confirm
    public static function confirmemail($id, $secret)
    {
        // Check User
        $row = UsersModel::fetchRow('password,secret,status', ['id ='], [$id]);
        if (!$row) {
            $mgs = sprintf(Txt("CONFIRM_EXPIRE"), CONFIG['SIGNUPTIMEOUT'] / 86400);
            Redirect(Url(), $mgs);
        }
        if ($row['status'] != "pending") {
            Redirect(Url(), Txt("ACCOUNT_ACTIVATED"));
        }
        if ($secret != $row['secret']) {
            Redirect(Url(), Txt("SIGNUP_ACTIVATE_LINK"));
        }

        // Set Secret
        $secret = mksecret();
        $stmt = UsersModel::update(['secret','status'], ['id =', 'secret =', 'status ='], [$secret, 'confirmed', $id, $row['secret'], 'pending']);
        if ($stmt == 0) {
            Redirect(Url(), Txt("SIGNUP_UNABLE"));
        }
        Redirect(Url('login'), Txt("ACCOUNT_ACTIVATED"));
    }

}