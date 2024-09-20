<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Validate;

/*
* Profile Change Data
*/
class ProfilechangeModel
{

    // Account Add Avatar todo files
    public static function addavatar($user_id)
    {
        $maxsize = 130 * 130;
        $file = $_FILES['file']['name'];
        $ext = substr($file, -3);
        $allowedfiles = array("gif","jpg","png");

        if($_FILES['file']['size'] > $maxsize) {
            showMessage(("profile/$user_id"), LANG["INVAILD_FILE_SIZE_IMAGE"]);
        }

        if (in_array($ext, $allowedfiles)) {
            move_uploaded_file($_FILES['file']['tmp_name'], "uploaded/avatars/$user_id.$ext");
            $avatar = URLROOT . "/uploaded/avatars/$user_id.$ext";
            DB::update('users', ['avatar'], ['id='], [$avatar, $user_id]);
            showMessage(("profile/$user_id"), LANG["UP_AVATAR"] . " OK");
        }
    }

    // Account password change
    public static function submitPassword($id)
    {
        $chpassword = Validate::Input('chpassword');
        $passagain = Validate::Input('passagain');
        if ((!$chpassword) || (!$passagain)) {
            showMessage(("profile/$id"), LANG["MISSING_FORM_DATA"]);
        } elseif (strlen($chpassword) < 6) {
            showMessage(("profile/$id"), LANG["PASSWORD_SHORT"]);
        } elseif ($chpassword != $passagain) {
            showMessage(("profile/$id"), LANG["PASSWORDS_NOT_MATCH"]);
        }
        $chpassword = password_hash($chpassword, PASSWORD_BCRYPT);
        $secret = mksecret();
        $in = DB::update('users', ['password', 'secret'], ['id='], [$chpassword,$secret,$id]);
        if ($in > 0) {
            showMessage('logout', LANG["PASSWORD_CHANGED_OK"]);
        } else {
            showMessage(("profile/$id"), LANG["ACTION_FAILED"]);
        }
    }

    // Account password change
    public static function changeemail($id)
    {
        $email = Validate::Input('email', 'email');

        // Set Secret
        $sec = mksecret();
        $obemail = rawurlencode($email);
        $sitename = URLROOT;

        // Send Email
        $body = file_get_contents(VIEW_PATH . "/user/email/changeemail.php");
        $body = str_replace("%usersname%", $id, $body);
        $body = str_replace("%sitename%", $sitename, $body);
        $body = str_replace("%usersip%", $_SERVER["REMOTE_ADDR"], $body);
        $body = str_replace("%usersid%", $id, $body);
        $body = str_replace("%userssecret%", $sec, $body);
        $body = str_replace("%obemail%", $obemail, $body);
        $body = str_replace("%newemail%", $email, $body);
        $TTMail = new TTMail();
        $TTMail->Send($email, "".CONFIG['SITENAME']." profile update confirmation", $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");

        // Update User Secret
        DB::update('users', ['editsecret'], ['id='], [$sec, $id]);
        showMessage(("profile/$id"), LANG["EMAIL_CHANGE_SEND"]);
    }

}