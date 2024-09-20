<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Validate;

/*
* Account Data
*/
class RecoverModel
{
    // Recover Password
    public static function recoverPassword()
    {
        // Check Inputs
		$email = Validate::Input('email', 'email');

        Validate::captcha_response($_POST['g-recaptcha-response']);

        // Get User By Email
        $arr = DB::fetchRow('users', 'id, username, email', ['email ='], [$email]);
        if (!$arr) {
            showMessage(URLROOT, LANG["EMAIL_ADDRESS_NOT_FOUND"]);
        }

        // Send Email
        if ($arr) {
            $sec = mksecret();
            $id = $arr['id'];
            $username = $arr['username']; // 06/01
            $emailmain = CONFIG['SITEEMAIL'];
            $url = URLROOT;
            $body = LANG["SOMEONE_FROM"] . " " . $_SERVER["REMOTE_ADDR"] . " " . LANG["MAILED_BACK"] . " ($email) " . LANG["BE_MAILED_BACK"] . " \r\n\r\n " . LANG["ACCOUNT_INFO"] . " \r\n\r\n " . LANG["USERNAME"] . ": " . $username . " \r\n " . LANG["CHANGE_PSW"] . "\n\n$url/recover/confirm/$id/$sec\n\n\n" . $url . "\r\n";
            $TTMail = new TTMail();
            $TTMail->Send($email, LANG["ACCOUNT_DETAILS"], $body, "", "-f$emailmain");
            DB::update('users', ['secret'], ['email='], [$sec, $email], 1);
            showMessage(URLROOT, sprintf(LANG['MAIL_RECOVER'], htmlspecialchars($email)));
        }

        showMessage(URLROOT, LANG["NOTHING_FOUND"]);
    }

    // Confirm Recover Password
    public static function changePassword($id, $secret)
    {
        // Check Inputs
        Validate::captcha_response($_POST['g-recaptcha-response']);
        $password = Validate::Input('password');
        $password1 = Validate::Input('password1');
        if (empty($password) || empty($password1)) {
             showMessage(URLROOT, LANG["MISSING_FORM_DATA"]);
        } elseif ($password != $password1) {
            showMessage(URLROOT, LANG["PASSWORD_NO_MATCH"]);
        }
        // Check User
        $count = DB::fetchColumn('users', 'COUNT(id)', ['id =', 'secret ='], [$id,$secret]);
        if ($count != 1) {
            showMessage(URLROOT, LANG["NO_SUCH_USER"]);
        }

        // Update User
        $newsec = mksecret();
        $wantpassword = password_hash($password, PASSWORD_BCRYPT);
        DB::update('users', ['password', 'secret'], ['id=', 'secret='], [$wantpassword, $newsec, $id, $secret]);

        showMessage(URLROOT, LANG["PASSWORD_CHANGED_OK"]);
    }

}