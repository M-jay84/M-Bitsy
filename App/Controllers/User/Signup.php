<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Ip;
use App\Models\AccountsModel;
use App\Models\CountriesModel;

/*
* Signup Controller
*/
class Signup extends Controller
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

    // User Signup
    public function index()
    {
        // Check IP
        if (CONFIG['IPCHECK']) {
            Ip::checkIP();
        }

        if (!empty($_POST)) {
            // Sign Account Up
            AccountsModel::signupUser();
        } else {
            $data = [
                'TITLE' => LANG["SIGNUP"],
                'CHECK_INVITE' => AccountsModel::checkinvite(),
                'COUNTRY' => CountriesModel::dropdown()
            ];
            $this->view('user-signup', $data, 'user');
        }
    }

}