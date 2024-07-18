<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\WarningsModel;

/*
* Warnings Controller
*/
class Adminwarning 
{
    // Warning Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Warnings
        $warnings = WarningsModel::getAll($USER);

        // Init Data
        $data = [
            'V_title' => Txt("WARNINGS"),
            'V_pager' => $warnings['pager'],
            'V_count' => $warnings['count'],
            'V_warnings' => $warnings['stmt'],
            'USER' => $USER
        ];

        // Load View
        template('warning', $data, 'admin');
    }

    // Warning Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        WarningsModel::deleteAllAdmin();
    }
    
}