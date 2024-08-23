<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Whos Where Controller
*/
class Adminwhoswhere extends Controller
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

    // Whos Where Default Page
    public function index()
    {
    
        // Get Data
        $users1 = UsersModel::select('id, username, page, last_access', ['enabled=', 'status='], ['yes','confirmed'], 'ORDER BY `last_access` DESC LIMIT 100');
        
        // Data For View
        $data = [
            'page_title' => ("WHOS_WHERE"),
            'V_users' => $users1
        ];

        // Load View
        $this->view('whoswhere', $data, 'admin');
    }

}