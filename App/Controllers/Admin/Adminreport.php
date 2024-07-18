<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\ReportsModel;

/*
* Report Controller
*/
class Adminreport 
{
    // Reports Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Check User Input
        $reports = ReportsModel::pagination();

        // Init Data
        $data = [
            'V_title' => Txt("REPORT"),
            'V_reports' => $reports['stmt'],
            'V_pager' => $reports['pager'],
            'USER' => $USER
        ];

        // Load View
        template('report', $data, 'admin');
    }

    // Reports Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        if ($_POST["mark"]) {
            ReportsModel::update();
        }
        
        if ($_POST["delete"]) {
            ReportsModel::deleteByIds();
        }

        Redirect(Url('adminreport'), Txt("ACTION_FAILED"));
    }

}