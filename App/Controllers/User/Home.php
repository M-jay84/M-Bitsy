<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ShoutboxModel;

/*
* Default Controller (Home Page)
*/
class Home extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(1, true);
    }

    // Admincp Default Page
    public function index()
    {
        
        $shouts = (new ShoutboxModel)->shouts(20);
        
        // Data For View
        $data = [
            'page_title' => LANG["HOME"],
            'current_user' => $this->current_user,
            'V_shouts' => $shouts
        ];

        // Load View
        $this->view('home', $data, 'user');
    }
}