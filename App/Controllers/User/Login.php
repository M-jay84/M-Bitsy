<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\AccountsModel;

/*
* Login Controller
*/
class Login 
{
    // Login Page
    public function index()
    {
        // Verify User/Guest
        $USER = new User(0, 0);

        // User Already Logged In
        if ($USER->has('id')) {
            Redirect(Url(), Txt("ALREADY_LOGGED"));
        }
        if (!empty($_POST) && csrf_check()) {
            AccountsModel::login();
        }
        $data = [
            'V_title' => Txt("LOGIN"),
            'USER' => $USER
        ];
        template('login', $data, 'user');
    }
}