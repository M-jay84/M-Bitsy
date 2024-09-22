<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\RecoverLogic;

/*
* Recover Controller
*/
class Recover extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if ($this->current_user->id) {
            showMessage(URLROOT, LANG["ALREADY_LOGGED"]);
        }
    }

    // Recover Page
    public function index()
    {
        if (!empty($_POST)) {
            // Get New Password
            RecoverLogic::recoverPassword();
        } else {
            $data = [
                'TITLE' => LANG['RECOVER']
            ];
            $this->template('user-recover', $data, 'user');
        }
    }

    // Confirm Recover
    public function confirm($id, $secret)
    {
        if (!intval($id) && !strlen($secret) == 20) {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }

        if (!empty($_POST)) {
            // Confirm New Password
            RecoverLogic::changePassword($id, $secret);
        } else {
            $data = [
                'TITLE' => LANG['RECOVER'],
                'ID' => $id,
                'SECRET' => $secret
            ];
            $this->template('user-recover-confirm', $data, 'user');
        }
    }
}