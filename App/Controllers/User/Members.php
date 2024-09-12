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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Members Page
    public function index()
    {
        // Check User
        if ($this->current_user->view_users != "yes" && !$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }

        // Search Users
        $results = UsersModel::getMembers();

        // Data For View
        $data = [
            'page_title' => 'Members',
            'V_groups' => $results['groups'],
            'V_users' => $results['array'],
            'V_pager' => $results['pager']
        ];

        // Load View
        $this->view('user-member', $data, 'user');
    }

}