<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Libs\Ip;
use App\Models\AccountsModel;
use App\Models\CountriesModel;

/*
* Signup Controller
*/
class Signup 
{
    // Verify User/Guest
    public function index()
    {
        // Verify User/Guest
        $USER = new User(0, 0);
        
        // Verify User Already Logged In
        if ($USER->has('id')) {
            Redirect(Url(), Txt("ALREADY_LOGGED"));
        }

        // Check IP
        if (CONFIG['IPCHECK']) {
            Ip::checkIP();
        }

        // Check Input Else Return To Form
        if (!empty($_POST) && csrf_check()) {
            AccountsModel::signupUser();
        } else {
            $invite_row = AccountsModel::checkinvite();
        
            $data = [
                'V_title' => Txt("SIGNUP"),
                'V_invite' => $invite_row,
                'V_country' => CountriesModel::dropdown(),
                'USER' => $USER
            ];
            template('signup', $data, 'user');
        }
    }

}