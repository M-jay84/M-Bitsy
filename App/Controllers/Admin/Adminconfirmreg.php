<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersLogic;

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
        $users = UsersLogic::usersToConfirm();

        // Data For View
        $data = [
            'TITLE' => LANG["REG_CONFIRM"],
            'COUNT' => $users['count'],
            'PAGER' => $users['pager'],
            'USER' => $users['users']
        ];

        // Load View
        $this->template('admin-confirmreg', $data, 'admin');
    }

    // Confirm Users Submit
    public function submit()
    {
        if (!empty($_POST)) {
            UsersLogic::confirmReg();
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }
    
}