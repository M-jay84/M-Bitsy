<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\GroupsModel;

/*
* Staff Controller
*/
class Staff 
{
    // Staff Page
    public function index()
    {
        $USER = new User(0, 2);
        // Get Staff Data
        $staff = GroupsModel::getStaffTable($USER);

        // Init Data
        $data = [
            'V_title' =>  Txt("STAFF"),
            'V_staff' => $staff['res'],
            'V_table' => $staff['table'],
            'USER' => $USER
        ];

        // Load View
        template('staff', $data, 'user');
    }

}