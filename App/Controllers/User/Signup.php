<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Ip;
use App\Models\AccountsLogic;
use App\Models\CountriesLogic;

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
            AccountsLogic::signupUser();
        } else {
            $data = [
                'TITLE' => LANG["SIGNUP"],
                'CHECK_INVITE' => AccountsLogic::checkinvite(),
                'COUNTRY' => CountriesLogic::dropdown()
            ];
            $this->template('user-signup', $data, 'user');
        }
    }

}