<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Inactive User Controller
*/
class Admininactive extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Inactive Users
    public function index()
    {
        // Inactivite Users
        $users = UsersModel::inactiveUsers();

        // Data For View
        $data = [
            'TITLE' => LANG["INACTIVE"],
            'DAYS' => $users['days'],
            'USERS' => $users['users'],
            'COUNT' => $users['count']
        ];
        
        // Load View
        $this->view('admin-inactive', $data, 'admin');
    }

    // Inactive Users Submit
    public function submit()
    {
        if (!empty($_POST)) {
            UsersModel::inactiveUsersSubmit($this->current_user->username);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}