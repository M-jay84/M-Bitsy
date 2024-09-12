<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\SnatchedModel;

/*
* Snatched Controller
*/
class Adminsnatch extends Controller
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

    // Hit & Runs Default Page
    public function index()
    {
        if (!CONFIG['HNR_ON']) {
            $this->showMessage(URLROOT, LANG["HNRS_BEEN_DISABLED"]);
        }

        // Pagination
        $snatched = SnatchedModel::join('snatched.hnr = ?', 'yes');

        // Data For View
        $data = [
            'page_title' => LANG["HNR"],
            'V_count' => $snatched['count'],
            'V_pager' => $snatched['pager'],
            'V_snatched' => $snatched['stmt']
        ];

        // Load View
        $this->view('admin-snatch', $data, 'admin');
    }

    // Hit & Runs Default Page
    public function submit()
    {
        SnatchedModel::deleteByIds();
    }
}