<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\AccountsModel;

/*
* Ban Controller
*/
class Confirm
{
    public function index()
    {
        Redirect(Url(), Txt("NOTHING_FOUND"));
    }

    // Email Reset Confirm
    public function confirmpassword($id, $secret, $email)
    {
        $USER = new User(0, 0);
        AccountsModel::confirmpassword($id, $secret, $email);
    }

    // Signup Email Confirm
    public function confirmemail($id, $secret)
    {
        $USER = new User(0, 0);
        AccountsModel::confirmemail($id, $secret);
    }

}