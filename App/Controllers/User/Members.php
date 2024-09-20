<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Members Controller
*/
class Members extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Members
    public function index()
    {
        // Check User
        if ($this->current_user->view_users != "yes" && !$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }

        // Search Users
        $users = UsersModel::getMembers();

        // Data For View
        $data = [
            'TITLE' => LANG["USERS"],
            'GROUPS' => $users['groups'],
            'USERS' => $users['array'],
            'PAGER' => $users['pager']
        ];

        // Load View
        $this->view('user-member', $data, 'user');
    }

}