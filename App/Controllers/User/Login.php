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
        $this->current_user = (new Auth)->user(0);
    }

    // Login Page
    public function index()
    {
        // User Already Logged In
        if ($this->current_user->id) {
            showMessage(Url(CONFIG['URLROOT']), LANG["ALREADY_LOGGED"]);
        }

        if (!empty($_POST) && csrf_check()) {
            (new AccountsModel)->login();
        }
        $data = [
            'page_title' => LANG["LOGIN"]
        ];
        $this->view('login', $data, 'user');
    }
}