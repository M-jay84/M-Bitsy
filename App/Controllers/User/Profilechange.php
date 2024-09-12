<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ProfilechangeModel;

/*
* Accout Controller
*/
class Profilechange extends Controller
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

    // Profile Avatar Upload Submit
    public function changeavatar($user_id)
    {
        if (!$this->current_user->isStaff && $this->current_user->id != $user_id )
        {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        if (!empty($_FILES['file'])) {
            ProfilechangeModel::addavatar($user_id);
        } else {
            $this->showMessage("profile/$user_id", LANG["ACTION_FAILED"]);
        }
    }

    // Profile Change Password Submit
    public function changepassword($user_id)
    {
        if (!$this->current_user->isStaff && $this->current_user->id != $user_id) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }


        if (!empty($_POST)) {
            ProfilechangeModel::submitPassword($user_id);
        } else {
            $this->showMessage("profile/$user_id", LANG["MISSING_FORM_DATA"]);
        }

    }

    // Profile Email Reset Submit
    public function changeemail($user_id)
    {
        if (!$this->current_user->isStaff && $this->current_user->id != $user_id) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        if (!empty($_POST)) {
            Profilechange::changeemail($user_id);
        } else {
            $this->showMessage("profile/$user_id", LANG["MISSING_FORM_DATA"]);
        }
    }

}