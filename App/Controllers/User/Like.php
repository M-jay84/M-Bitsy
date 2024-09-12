<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\LikesModel;

/*
* Like Controller
*/
class Like extends Controller
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

    // Like Submit
    public function index($id, $type)
    {
        LikesModel::switch($id, $type, $this->current_user);
    }

}