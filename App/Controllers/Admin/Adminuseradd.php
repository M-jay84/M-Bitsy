<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Add User Controller
*/
class Adminuseradd 
{
    // No Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            UsersModel::adduser();
        } else {
            // Init Data
            $data = [
                'V_title' => Txt('ADD_USER'),
            ];
        
            // Load View
            template('user_add', $data, 'admin');
        }
    }
    
}