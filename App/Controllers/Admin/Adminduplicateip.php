<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Duplicate IP Controller
*/
class Adminduplicateip extends Controller
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

    // Duplicate Ips Default Page
    public function index()
    {
    
        // Get Ip Data
        $users = UsersModel::duplicateIps();
        
        // Data For View
        $data = [
            'page_title' => LANG["DUPLICATEIP"],
            'V_users' => $users['stmt']
        ];

        // Load View
        $this->view('duplicateip', $data, 'admin');
    }

}