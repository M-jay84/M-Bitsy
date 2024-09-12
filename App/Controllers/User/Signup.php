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
            $this->showMessage(URLROOT, LANG["ALREADY_LOGGED"]);
        }
    }

    // Verify User/Guest
    public function index()
    {
        // Check IP
        if (CONFIG['IPCHECK']) {
            Ip::checkIP();
        }

        // Check Input Else Return To Form
        if (!empty($_POST)) {
            AccountsModel::signupUser();
        } else {
            $data = [
                'page_title' => LANG["SIGNUP"],
                'V_invite' => AccountsModel::checkinvite(),
                'V_country' => CountriesModel::dropdown()
            ];
            $this->view('user-signup', $data, 'user');
        }
    }

}