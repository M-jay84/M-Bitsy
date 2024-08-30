<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\AccountsModel;

/*
* Recover Controller
*/
class Recover extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(0);
    }

    // Recover Page
    public function index()
    {
        // User Already Logged In
        if ($this->current_user->id) {
            showMessage(Url(), LANG["ALREADY_LOGGED"]);
        }
        if (!empty($_POST) && csrf_check()) {
            (new AccountsModel)->recoverPassword();
        } else {
            $data = [
                'page_title' => LANG['RECOVER']
            ];
            $this->view('recover', $data, 'user');
        }
    }

    // Confirm Recover
    public function confirm($id, $secret)
    {
        // User Already Logged In
        if ($this->current_user->id) {
            showMessage(Url(), LANG["ALREADY_LOGGED"]);
        }
        if (!Id($id) && !strlen($secret) == 20) {
            showMessage(Url(), LANG["NOTHING_FOUND"]);
        }

        if (!empty($_POST) && csrf_check()) {
            (new AccountsModel)->changePassword($id, $secret);
        } else {
            $data = [
                'page_title' => LANG['RECOVER'],
                'V_id' => $id,
                'V_secret' => $secret
            ];
            $this->view('recover_confirm', $data, 'user');
        }
    }
}