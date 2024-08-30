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
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Confirm User Default Page
    public function index()
    {
    
        // Pagination
        $count = UsersModel::fetchColumn('count(id)', ['status =', 'invited_by ='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, Url('adminconfirmreg?'));
        $users = UsersModel::fetchAll('id,username,email,added,ip', ['status =','invited_by ='], ['pending',0], "ORDER BY `added` DESC $limit");

        // Data For View
        $data = [
            'page_title' => LANG["REG_CONFIRM"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users
        ];

        // Load View
        $this->view('confirmreg', $data, 'admin');
    }

    // Confirm Form Submit
    public function submit()
    {
    
        UsersModel::confirmReg();
    }
    
}