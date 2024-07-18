<?php

namespace App\Controllers\Admin;

use App\Libs\User;

/*
* Admin Default Controller
*/
class Admincp 
{
    // Admincp Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Init Data
        $data = [
            'V_title' => Txt('STAFF'),
            'USER' => $USER
        ];

        // Load View
        template('shoutbox', $data, 'admin');
    }
    
}