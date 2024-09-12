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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Warning Default Page
    public function index()
    {
        // Get Warnings
        $warnings = WarningsModel::getAll($this->current_user->id);

        // Data For View
        $data = [
            'page_title' => LANG["WARNINGS"],
            'V_pager' => $warnings['pager'],
            'V_count' => $warnings['count'],
            'V_warnings' => $warnings['stmt']
        ];

        // Load View
        $this->view('admin-warning', $data, 'admin');
    }

    // Warning Form Submit
    public function submit()
    {
        WarningsModel::deleteAllAdmin();
    }
    
}