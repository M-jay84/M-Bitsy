<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Confirm Registration Controller
*/
class Adminconfirmreg extends Controller
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

    // Confirm Users
    public function index()
    {
        // Show Users
        $users = UsersModel::usersToConfirm();

        // Data For View
        $data = [
            'page_title' => LANG["REG_CONFIRM"],
            'V_count' => $users['count'],
            'V_pager' => $users['pager'],
            'V_users' => $users['users']
        ];

        // Load View
        $this->view('admin-confirmreg', $data, 'admin');
    }

    // Confirm Users Submit
    public function submit()
    {
        UsersModel::confirmReg();
    }
    
}