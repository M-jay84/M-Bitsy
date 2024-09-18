<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Validate;
use App\Models\WarningsModel;

/*
* Warning Controller
*/
class Warning extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // User Warnings Page
    public function index($user_id)
    {
        // Check Current User
        if (!$this->current_user->isStaff && $this->current_user->id != $user_id) {
            $this->showMessage(URLROOT, LANG['NO_PERMISSION']);
        }

        // Data For View
        $data = array(
            'page_title' => LANG["USER_DETAILS_FOR"] . coloredname($user_id),
            'user_id' => $user_id,
            'warnings' => WarningsModel::getByUserId($user_id),
            'current_user' => $this->current_user
        );

        // Load View
        $this->view('user-warning', $data, 'user');
    }

    // Warnings Form Submit
    public function submit($userid)
    {
        // Check User
        if (!$this->current_user->isStaff) {
            $this->showMessage("profile/$userid", LANG["NO_PERMISSION"]);
        }

        // Add New Warning
        WarningsModel::newwarn($userid);
    }

    // Delete User Warning
    public function delete($userid)
    {
        // Check User
        if (!$this->current_user->isStaff) {
            $this->showMessage("profile/$userid", LANG["NO_PERMISSION"]);
        }

        // Set Data
        $data = [
            'current_user' => $this->current_user,
            'userid' => $userid,
            'post_id' => Validate::multiIds("ids")
        ];

        // Delete Warning/s
        WarningsModel::deleteAll($data);
    }
}