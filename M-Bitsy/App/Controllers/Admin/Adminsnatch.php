<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\SnatchedLogic;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Snatch/H&R/Peer History
    public function index()
    {
        // Show All Peer History
        $snatched = SnatchedLogic::join('snatched.hnr = ?', 'yes');

        // Data For View
        $data = [
            'TITLE' => LANG["HNR"],
            'COUNT' => $snatched['count'],
            'PAGER' => $snatched['pager'],
            'SNATCHED' => $snatched['stmt']
        ];

        // Load View
        $this->template('admin-snatch', $data, 'admin');
    }

    // Delete Record Of Snatch/H&R/Peer History
    public function submit()
    {
        if (!empty($_POST)) {
            SnatchedLogic::deleteByIds();
        } else {
            showMessage(URLROOT, LANG['NOTHING_SELECTED']);
        }       
    }
}