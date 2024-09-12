<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\RecoverModel;

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
            $this->showMessage(URLROOT, LANG["ALREADY_LOGGED"]);
        }
    }

    // Recover Page
    public function index()
    {
        if (!empty($_POST)) {
            RecoverModel::recoverPassword();
        } else {
            $data = [
                'page_title' => LANG['RECOVER']
            ];
            $this->view('user-recover', $data, 'user');
        }
    }

    // Confirm Recover
    public function confirm($id, $secret)
    {
        if (!intval($id) && !strlen($secret) == 20) {
            $this->showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }

        if (!empty($_POST)) {
            RecoverModel::changePassword($id, $secret);
        } else {
            $data = [
                'page_title' => LANG['RECOVER'],
                'V_id' => $id,
                'V_secret' => $secret
            ];
            $this->view('user-recover-confirm', $data, 'user');
        }
    }
}