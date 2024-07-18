<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Whos Where Controller
*/
class Adminwhoswhere 
{
    // Whos Where Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Data
        $users1 = UsersModel::select('id, username, page, last_access', ['enabled=', 'status='], ['yes','confirmed'], 'ORDER BY `last_access` DESC LIMIT 100');
        
        // Init Data
        $data = [
            'V_title' => ("WHOS_WHERE"),
            'V_users' => $users1,
            'USER' => $USER
        ];

        // Load View
        template('whoswhere', $data, 'admin');
    }

}