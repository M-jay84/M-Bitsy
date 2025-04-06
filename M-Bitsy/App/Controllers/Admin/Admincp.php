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
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Admincp Default Page
    public function index()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['STAFF'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('admin-home', $data, 'admin');
    }
    
}