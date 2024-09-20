<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\BansModel;

/*
* Ban Controller
*/
class Adminban extends Controller
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

    // Email & IP Bans
    public function index($type = 'email')
    {
        if (!empty($_POST)) {
            // Add New Ban
            BansModel::insert($this->current_user);
        } else {
            // Show All Bans
            $bans = BansModel::getAll($type);

            // Data For View
            $data = [
                'TITLE' => LANG["BANNED"],
                'BANS' => $bans['bans'],
                'PAGER' => $bans['pager'],
                'USER' => $this->current_user
            ];

            // Load View
            $this->view('admin-ban', $data, 'admin');
        }
    }

    // Delete Bans
    public function delete()
    { 
        if (!empty($_POST)) {
            BansModel::delete($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }
}