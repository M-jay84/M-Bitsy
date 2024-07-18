<?php

namespace App\Controllers\User;

use App\Libs\User;

/*
* Default Controller (Home Page)
*/
class Home 
{
    // Admincp Default Page
    public function index()
    {
        $USER = new User(0, 1, true);
        // Init Data
        $data = [
            'V_title' => Txt("Home"),
            'USER' => $USER
        ];

        // Load View
        template('home', $data, 'home');
    }
}