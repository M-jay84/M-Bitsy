<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\AccountsModel;

/*
* Recover Controller
*/
class Recover 
{
    // Recover Page
    public function index()
    {
        $USER = new User(0, 0);     
           
        // User Already Logged In
        if ($USER->has('id')) {
            Redirect(Url(), Txt("ALREADY_LOGGED"));
        }
        if (!empty($_POST) && csrf_check()) {
            AccountsModel::recoverPassword();
        } else {
            $data = [
                'V_title' => Txt('RECOVER'),
                'USER' => $USER
            ];
            template('recover', $data, 'user');
        }
    }

    // Confirm Recover
    public function confirm($id, $secret)
    {
        $USER = new User(0, 0);     
           
        // User Already Logged In
        if ($USER->has('id')) {
            Redirect(Url(), Txt("ALREADY_LOGGED"));
        }
        if (!Id($id) && !strlen($secret) == 20) {
            Redirect(Url(), Txt("NOTHING_FOUND"));
        }

        if (!empty($_POST) && csrf_check()) {
            AccountsModel::changePassword($id, $secret);
        } else {
            $data = [
                'V_title' => Txt('RECOVER'),
                'V_id' => $id,
                'V_secret' => $secret,
                'USER' => $USER
            ];
            template('recover_confirm', $data, 'user');
        }
    }
}