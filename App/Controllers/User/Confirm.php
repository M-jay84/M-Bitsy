<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\AccountsModel;

/*
* Ban Controller
*/
class Confirm extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(0);
    }

    public function index()
    {
        showMessage(Url(), LANG["NOTHING_FOUND"]);
    }

    // Email Reset Confirm
    public function confirmpassword($id, $secret, $email)
    {
        (new AccountsModel)->confirmpassword($id, $secret, $email);
    }

    // Signup Email Confirm
    public function confirmemail($id, $secret)
    {
        (new AccountsModel)->confirmemail($id, $secret);
    }

}