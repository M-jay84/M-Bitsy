<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TorrentsLogic;

/*
* Reseed Controller
*/
class Reseed extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Request Reseed Form Submit
    public function index($id)
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        TorrentsLogic::reseed($id, $this->current_user);
    }

}