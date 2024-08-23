<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Add User Controller
*/
class Adminuseradd extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // No Default Page
    public function index()
    {
    
        if (!empty($_POST)) {
            UsersModel::adduser();
        } else {
            // Data For View
            $data = [
                'page_title' => LANG['ADD_USER'],
            ];
        
            // Load View
            $this->view('user_add', $data, 'admin');
        }
    }
    
}