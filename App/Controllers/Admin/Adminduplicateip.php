<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Duplicate IP Controller
*/
class Adminduplicateip 
{
    // Duplicate Ips Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Ip Data
        $users = UsersModel::duplicateIps();
        
        // Init Data
        $data = [
            'V_title' => Txt("DUPLICATEIP"),
            'V_users' => $users['stmt'],
            'USER' => $USER
        ];

        // Load View
        template('duplicateip', $data, 'admin');
    }

}