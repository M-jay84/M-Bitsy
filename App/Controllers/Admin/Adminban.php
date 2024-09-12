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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Email & IP Bans
    public function index($type = 'email')
    {
        if (!empty($_POST)) {
            BansModel::insert($this->current_user);
        } else {
            $bans = BansModel::getAll($type);

            // Data For View
            $data = [
                'page_title' => LANG["BANNED"],
                'V_type' => $type,
                'V_bans' => $bans['bans'],
                'V_pager' => $bans['pager'],
                'current_user' => $this->current_user
            ];

            // Load View
            $this->view('admin-ban', $data, 'admin');
        }
    }

    // Delete Bans
    public function delete()
    {
        BansModel::delete($this->current_user);
    }
}