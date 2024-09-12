<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ReportsModel;

/*
* Report Controller
*/
class Adminreport extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Reports Default Page
    public function index()
    {
        // Check User Input
        $reports = ReportsModel::pagination();

        // Data For View
        $data = [
            'page_title' => LANG["REPORT"],
            'V_reports' => $reports['stmt'],
            'V_pager' => $reports['pager']
        ];

        // Load View
        $this->view('admin-report', $data, 'admin');
    }

    // Reports Form Submit
    public function submit()
    {
        if ($_POST["mark"]) {
            ReportsModel::update($this->current_user->id);
        }
        
        if ($_POST["delete"]) {
            ReportsModel::deleteByIds();
        }

        $this->showMessage('adminreport', LANG["ACTION_FAILED"]);
    }

}