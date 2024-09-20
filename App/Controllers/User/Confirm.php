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
    public function index(){die('no access');}

    // Profile Email Reset Confirm
    public function confirmprofileemail($id, $secret, $email)
    {
        ConfirmModel::confirmprofileemail($id, $secret, $email);
    }

    // Signup Email Confirm
    public function confirmemail($id, $secret)
    {
        ConfirmModel::confirmemail($id, $secret);
    }

}