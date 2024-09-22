<?php

namespace App\Models;

use App\Libs\DB;

/*
* Account Data
*/
class ConfirmLogic
{
    // Profile Email Reset Confirm
    public static function confirmprofileemail($id, $secret, $email)
    {
        $editsecret = DB::fetchColumn('users', 'editsecret', ['enabled =', 'status =', 'editsecret =', 'id ='], ['yes', 'confirmed', $secret, $id]);
       
        if (!$editsecret) {
            showMessage(URLROOT,  LANG["NOTHING_FOUND"]);
        }

        if ($secret != $editsecret) {
            showMessage(URLROOT,  LANG["NOTHING_FOUND"]);
        }

        // Update User Editsecret
        DB::update('users', ['editsecret','email'], ['id=', 'editsecret='], ['', $email, $id, $editsecret]);
        showMessage(URLROOT, LANG["SUCCESS"]);
    }

    // Signup Email Confirm
    public static function confirmemail($id, $secret)
    {
        // Check User
        $row = DB::fetchRow('users', 'password,secret,status', ['id ='], [$id]);
        if (!$row) {
            $mgs = sprintf(LANG["CONFIRM_EXPIRE"], CONFIG['SIGNUPTIMEOUT'] / 86400);
            showMessage(URLROOT, $mgs);
        }
        if ($row['status'] != "pending") {
            showMessage(URLROOT, LANG["ACCOUNT_ACTIVATED"]);
        }
        if ($secret != $row['secret']) {
            showMessage(URLROOT, LANG["SIGNUP_ACTIVATE_LINK"]);
        }

        // Set Secret
        $secret = mksecret();
        $stmt = DB::update('users', ['secret','status'], ['id =', 'secret =', 'status ='], [$secret, 'confirmed', $id, $row['secret'], 'pending']);
        if ($stmt == 0) {
            showMessage(URLROOT, LANG["SIGNUP_UNABLE"]);
        }
        showMessage('login', LANG["ACCOUNT_ACTIVATED"]);
    }

}