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
            $this->showMessage(URLROOT, LANG["ALREADY_LOGGED"]);
        }
    }

    // Login Page
    public function index()
    {
        if (!empty($_POST)) {
            AccountsModel::login();
        }
    
        $data = [
            'page_title' => LANG["LOGIN"]
        ];
        $this->view('user-login', $data, 'user');
    }
}