<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Members Controller
*/
class Members 
{
    // Members Page
    public function index()
    {
        $USER = new User(0, 2);
        // Check User
        if (!$USER->permission('view_users', 0)) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }

        // Search Users
        $results = UsersModel::getMembers();

        // Init Data
        $data = [
            'V_title' => 'Members',
            'V_groups' => $results['groups'],
            'V_users' => $results['array'],
            'V_pager' => $results['pager'],
            'USER' => $USER
        ];

        // Load View
        template('member', $data, 'user');
    }

}