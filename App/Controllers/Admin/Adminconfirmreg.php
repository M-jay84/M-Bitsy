<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Confirm Registration Controller
*/
class Adminconfirmreg
{
    // Confirm User Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Pagination
        $count = UsersModel::fetchColumn('count(id)', ['status =', 'invited_by ='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, Url('adminconfirmreg?'));
        $users = UsersModel::fetchAll('id,username,email,added,ip', ['status =','invited_by ='], ['pending',0], "ORDER BY `added` DESC $limit");

        // Init Data
        $data = [
            'V_title' => Txt("REG_CONFIRM"),
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users,
            'USER' => $USER
        ];

        // Load View
        template('confirmreg', $data, 'admin');
    }

    // Confirm Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        UsersModel::confirmReg();
    }
    
}