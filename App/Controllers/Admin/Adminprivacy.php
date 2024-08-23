<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Privacy Controller
*/
class Adminprivacy extends Controller
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

    // Privacy Default Page
    public function index($type = 'ALL')
    {
    
        $users = UsersModel::privacyPagination($type);

        // Data For View
        $data = [
            'page_title' => LANG["PRIVACY_LEVEL"],
            'V_users' => $users['stmt'],
            'V_pager' => $users['pager']
        ];

        // Load View
        $this->view('privacy', $data, 'admin');
    }

}