<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\LogsLogic;

/*
* Log Controller
*/
class Adminlog extends Controller
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

    // Logs
    public function index()
    {
        // Show All Logs
        $logs = LogsLogic::getAll();

        // Data For View
        $data = [
            'TITLE' => LANG["SITE_LOG"],
            'PAGER' => $logs['pager'],
            'LOGS' => $logs['logs']
        ];

        // Load View
        $this->template('admin-log', $data, 'admin');
    }

    // Delete Form
    public function delete()
    {
        if (!empty($_POST)) {
            LogsLogic::delete();
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}