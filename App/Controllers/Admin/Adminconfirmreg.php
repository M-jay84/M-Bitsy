<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Confirm User Default Page
    public function index()
    {
        // Pagination
        $count = DB::fetchColumn('users', 'count(id)', ['status =', 'invited_by ='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, 'adminconfirmreg?');
        $users = DB::newSelect('users', 'id,username,email,added,ip', ['status =','invited_by ='], ['pending',0], "ORDER BY `added` DESC $limit");

        // Data For View
        $data = [
            'page_title' => LANG["REG_CONFIRM"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users
        ];

        // Load View
        $this->view('admin-confirmreg', $data, 'admin');
    }

    // Confirm Form Submit
    public function submit()
    {
        UsersModel::confirmReg();
    }
    
}