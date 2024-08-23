<?php

namespace App\Controllers\Admin;

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
        $this->current_user = $this->currentuser(2);
        if (!$this->current_user->isStaff) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Email & IP Bans
    public function index($type = 'email')
    {
        if (!empty($_POST)) {
            (new BansModel)->insert($this->current_user);
        } else {
            // Get Ban Data
            $bans = (new BansModel)->getAll($type);

            // Data For View
            $data = [
                'page_title' => LANG["BANNED"],
                'V_type' => $type,
                'V_bans' => $bans['bans'],
                'V_pager' => $bans['pager'],
                'current_user' => $this->current_user
            ];

            // Load View
            $this->view('ban', $data, 'admin');
        }
    }

    // Delete Bans
    public function delete()
    {
        (new BansModel)->delete($this->current_user);
    }
}