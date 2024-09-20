<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\AccountsModel;

/*
* Login Controller
*/
class Login extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if ($this->current_user->loggedin) {
            showMessage(URLROOT, LANG["ALREADY_LOGGED"]);
        }
    }

    // Login
    public function index()
    {
        if (!empty($_POST)) {
            // Log User In
            AccountsModel::login();
        }
    
        // Data For View
        $data = [
            'TITLE' => LANG["LOGIN"]
        ];

        // Load View
        $this->view('user-login', $data, 'user');
    }
}