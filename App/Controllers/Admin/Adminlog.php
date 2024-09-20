<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\LogsModel;

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
        $logs = LogsModel::getAll();

        // Data For View
        $data = [
            'TITLE' => LANG["SITE_LOG"],
            'PAGER' => $logs['pager'],
            'LOGS' => $logs['logs']
        ];

        // Load View
        $this->view('admin-log', $data, 'admin');
    }

    // Delete Form
    public function delete()
    {
        LogsModel::delete();
    }

}