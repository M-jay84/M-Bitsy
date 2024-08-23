<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\AccountsModel;

/*
* Accout Controller
*/
class Account extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    public function index()
    {
        showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
    }

    // Profile Avatar Upload Submit
    public function avatar($id)
    {
    
        if ($this->current_user->class < CONFIG['_MODERATOR'] or $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }

        if (isset($_FILES["upfile"])) {
            $data['current_user'] = $this->current_user;
            $data['userid'] = $id;
            (new AccountsModel)->addavatar($id);
        } else {
            showMessage(Url("profile/$id"), LANG["ACTION_FAILED"]);
        }
    }

    // Profile Change Password Submit
    public function changepassword($id)
    {
    
        if ($this->current_user->class < CONFIG['_MODERATOR'] or $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }

        if (!empty($_POST)) {
            (new AccountsModel)->submitPassword($id);
        } else {
            showMessage(Url("profile/$id"), LANG["MISSING_FORM_DATA"]);
        }

    }

    // Profile Email Reset Submit
    public function changeemail($id)
    {
    
        if ($this->current_user->class < CONFIG['_MODERATOR'] or $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }

        if (!empty($_POST)) {
            $data['current_user'] = $this->current_user;
            $data['userid'] = $id;
            (new AccountsModel)->changeemail($data);
        } else {
            showMessage(Url("profile/$id"), LANG["MISSING_FORM_DATA"]);
        }
    }

}