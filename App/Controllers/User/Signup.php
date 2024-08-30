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
        $this->current_user = (new Auth)->user(0);
    }

    // Verify User/Guest
    public function index()
    {
        // Verify User Already Logged In
        if ($this->current_user->id) {
            showMessage(Url(), LANG["ALREADY_LOGGED"]);
        }

        // Check IP
        if (CONFIG['IPCHECK']) {
            Ip::checkIP();
        }

        // Check Input Else Return To Form
        if (!empty($_POST) && csrf_check()) {
            (new AccountsModel)->signupUser();
        } else {
            $invite_row = (new AccountsModel)->checkinvite();
        
            $data = [
                'page_title' => LANG["SIGNUP"],
                'V_invite' => $invite_row,
                'V_country' => (new CountriesModel)->dropdown()
            ];
            $this->view('signup', $data, 'user');
        }
    }

}