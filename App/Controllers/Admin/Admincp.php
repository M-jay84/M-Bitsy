<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;

/*
* Admin Default Controller
*/
class Admincp extends Controller
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

    // Admincp Default Page
    public function index()
    {
    
        // Data For View
        $data = [
            'page_title' => LANG['STAFF'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('admincp', $data, 'admin');
    }
    
}