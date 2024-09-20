<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\EmailModel;
use App\Models\GroupsModel;

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
        $groups = GroupsModel::getGroups();
        
        // Data For View
        $data = [
            'TITLE' => LANG["MASS_EMAIL"],
            'GROUPS' => $groups
        ];

        // Load View
        $this->view('admin-email', $data, 'admin');
    }

    // Mass Email Submit
    public function sendemail()
    {
        // Check User Input
        if (!empty($_POST)) {
            EmailModel::emailAll($this->current_user);
            showMessage('adminemail', "<b><font color='#ff0000'>Mass mail sent....</font></b>");
        } else {
            showMessage(URLROOT, LANG['NOTHING_FOUND']);
        }

    }

}