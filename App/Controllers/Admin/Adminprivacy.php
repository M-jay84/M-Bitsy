<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Privacy Controller
*/
class Adminprivacy 
{
    // Privacy Default Page
    public function index($type = 'ALL')
    {
        $USER = new User(_MODERATOR, 2);
        $users = UsersModel::privacyPagination($type);

        // Init Data
        $data = [
            'V_title' => Txt("PRIVACY_LEVEL"),
            'V_users' => $users['stmt'],
            'V_pager' => $users['pager'],
            'USER' => $USER
        ];

        // Load View
        template('privacy', $data, 'admin');
    }

}