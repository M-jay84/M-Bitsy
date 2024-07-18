<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\WarningsModel;

/*
* Warning Controller
*/
class Warning 
{
    // User Warn Default Page
    public function index($id)
    {
        $USER = new User(0, 2);
        // Get Warnings
        $warnings = WarningsModel::getAll($USER, $id);
        
        // Init Data
        $data = [
            'V_title' => Txt("USER_DETAILS_FOR") . coloredname($id),
            'V_warnings' => $warnings['stmt'],
            'V_count' => $warnings['count'],
            'V_id' => $id,
            'USER' => $USER
        ];
        
        // Load View
        template('warning', $data, 'user');
    }

    // User Warn Form Submit
    public function submit($userid)
    {
        $USER = new User(0, 2);
        WarningsModel::newwarn($USER, $userid);
    }

    // Delete User Warning
    public function delete($id)
    {
        $USER = new User(0, 2);
        WarningsModel::deleteAll($USER, $id);
    }
    
}