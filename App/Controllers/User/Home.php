<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;

/*
* Default Controller (Home Page)
*/
class Home extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth(true);

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Admincp Default Page
    public function index()
    {
        // Data For View
        $data = [
            'page_title' => LANG["HOME"],
            'current_user' => $this->current_user,
        ];

        // Load View
        $this->view('user-home', $data, 'user');
    }
}