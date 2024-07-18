<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\AccountsModel;

/*
* Accout Controller
*/
class Account 
{
    public function index()
    {
        Redirect(Url(), Txt("NO_PERMISSION"));
    }

    // Profile Avatar Upload Submit
    public function avatar($id)
    {
        $USER = new User(0, 2);
        if ($USER->isStaff($id)) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        if (isset($_FILES["upfile"])) {
            AccountsModel::addavatar($id);
        } else {
            Redirect(Url("profile/$id"), Txt("ACTION_FAILED"));
        }
    }

    // Profile Change Password Submit
    public function changepassword($id)
    {
        $USER = new User(0, 2);
        if ($USER->isStaff($id)) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        if (!empty($_POST)) {
            AccountsModel::submitPassword($id);
        } else {
            Redirect(Url("profile/$id"), Txt("MISSING_FORM_DATA"));
        }

    }

    // Profile Email Reset Submit
    public function changeemail($id)
    {
        $USER = new User(0, 2);
        if ($USER->isStaff($id)) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        if (!empty($_POST)) {
            AccountsModel::changeemail($id);
        } else {
            Redirect(Url("profile/$id"), Txt("MISSING_FORM_DATA"));
        }
    }

}