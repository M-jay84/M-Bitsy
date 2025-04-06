<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\EmailLogic;
use App\Models\GroupsLogic;

/*
* Mass Email Controller
*/
class Adminemail extends Controller
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

    // Mass Email
    public function index()
    {
        // Get All Groups
        $groups = GroupsLogic::getGroups();
        
        // Data For View
        $data = [
            'TITLE' => LANG["MASS_EMAIL"],
            'GROUPS' => $groups
        ];

        // Load View
        $this->template('admin-email', $data, 'admin');
    }

    // Mass Email Submit
    public function sendemail()
    {
        if (!empty($_POST)) {
            EmailLogic::emailAll($this->current_user);
        } else {
            showMessage(URLROOT, LANG['NOTHING_FOUND']);
        }
    }

}