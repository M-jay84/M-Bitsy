<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ConfirmModel;

/*
* Ban Controller
*/
class Confirm extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    // Email Reset Confirm
    public function confirmpassword($id, $secret, $email)
    {
        ConfirmModel::confirmpassword($id, $secret, $email);
    }

    // Signup Email Confirm
    public function confirmemail($id, $secret)
    {
        ConfirmModel::confirmemail($id, $secret);
    }

}