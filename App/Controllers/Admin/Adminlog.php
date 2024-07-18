<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\LogsModel;

/*
* Log Controller
*/
class Adminlog 
{
    // Log Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        $logs = LogsModel::getAll();

        // Init Data
        $data = [
            'V_title' => Txt("SITE_LOG"),
            'V_pager' => $logs['pager'],
            'V_logs' => $logs['logs'],
            'USER' => $USER
        ];

        // Load View
        template('log', $data, 'admin');
    }

    // Log Form Submit
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        LogsModel::delete();
    }

}