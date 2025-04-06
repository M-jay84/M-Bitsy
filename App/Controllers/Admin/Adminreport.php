<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ReportsLogic;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Reports
    public function index()
    {
        // Get Reports
        $reports = ReportsLogic::pagination();

        // Data For View
        $data = [
            'TITLE' => LANG["REPORT"],
            'REPORTS' => $reports['stmt'],
            'PAGER' => $reports['pager']
        ];

        // Load View
        $this->template('admin-report', $data, 'admin');
    }

    // Reports Form Submit
    public function submit()
    {
        if ($_POST["mark"]) {
            ReportsLogic::update($this->current_user->id);
        } elseif ($_POST["delete"]) {
            ReportsLogic::deleteByIds();
        }

        showMessage('adminreport', LANG["ACTION_FAILED"]);
    }

}