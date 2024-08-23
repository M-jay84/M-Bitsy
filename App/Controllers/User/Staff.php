<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\GroupsModel;

/*
* Staff Controller
*/
class Staff extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Staff Page
    public function index()
    {
        $data['current_user'] = $this->current_user;
    
        // Get Staff Data
        $staff = (new GroupsModel)->getStaffTable();

        // Data For View
        $data = [
            'page_title' =>  LANG["STAFF"],
            'V_staff' => $staff['res'],
            'V_table' => $staff['table']
        ];

        // Load View
        $this->view('staff', $data, 'user');
    }

}