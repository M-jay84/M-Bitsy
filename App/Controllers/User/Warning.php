<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\WarningsLogic;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // User Warnings
    public function index($user_id)
    {
        // Check Current User
        if (!$this->current_user->isStaff && $this->current_user->id != $user_id) {
            showMessage(URLROOT, LANG['NO_PERMISSION']);
        }

        // Data For View
        $data = array(
            'TITLE' => LANG["USER_DETAILS_FOR"] . coloredname($user_id),
            'USER_ID' => $user_id,
            'WARNINGS' => WarningsLogic::getByUserId($user_id),
            'current_user' => $this->current_user
        );

        // Load View
        $this->template('user-warning', $data, 'user');
    }

    // Warnings Form Submit
    public function submit($userid)
    {
        // Check User
        if (!$this->current_user->isStaff) {
            showMessage("profile/$userid", LANG["NO_PERMISSION"]);
        }

        if(!empty($_POST)) {
            // Add New Warning
            WarningsLogic::newwarn($userid);
        } else {
            showMessage("warning/$userid", LANG["NOTHING_SELECTED"]);
        }
    }

    // Delete User Warning
    public function delete($userid)
    {
        // Check User
        if (!$this->current_user->isStaff) {
            showMessage("profile/$userid", LANG["NO_PERMISSION"]);
        }

        if(!empty($_POST)) {
            // Delete Warning/s
            WarningsLogic::deleteAll($userid, $this->current_user);
        } else {
            showMessage("warning/$userid", LANG["NOTHING_SELECTED"]);
        }
    }
}