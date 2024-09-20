<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\WarningsModel;

/*
* Warnings Controller
*/
class Adminwarning extends Controller
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

    // Warnings
    public function index()
    {
        // Get Warnings
        $warnings = WarningsModel::getAll();

        // Data For View
        $data = [
            'TITLE' => LANG["WARNINGS"],
            'PAGER' => $warnings['pager'],
            'COUNT' => $warnings['count'],
            'WARNINGS' => $warnings['stmt']
        ];

        // Load View
        $this->view('admin-warning', $data, 'admin');
    }

    // Warning Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            WarningsModel::deleteAllAdmin();
        } else {
            showMessage(URLROOT, LANG['NOTHING_SELECTED']);
        }  
    }
    
}