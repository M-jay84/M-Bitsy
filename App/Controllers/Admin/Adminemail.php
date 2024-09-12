<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Models\GroupsModel;
use App\Models\LogsModel;
use PDO;
use App\Libs\TTMail;
use App\Libs\Controller;
use App\Models\EmailModel;
use App\Models\UsersModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Mass Email Default Page
    public function index()
    {
        // Data For View
        $data = [
            'page_title' => LANG["MASS_EMAIL"],
            'V_groups' => GroupsModel::getGroups()
        ];

        // Load View
        $this->view('admin-email', $data, 'admin');
    }

    // Mass Email Form Submit
    public function sendemail()
    {
        // Check User Input
        if (!empty($_POST)) {
            EmailModel::emailAll($this->current_user);
            $this->showMessage('adminemail', "<b><font color='#ff0000'>Mass mail sent....</font></b>");
        } else {
            $this->showMessage(URLROOT, LANG['NOTHING_FOUND']);
        }

    }

}