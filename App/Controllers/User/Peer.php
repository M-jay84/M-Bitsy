<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\PeersModel;
use App\Models\UsersModel;

/*
* Peers Controller
*/
class Peer extends Controller
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

    // No Default Page // todo
    public function index($type, $id)
    {
        // Check User Input
        $user =  DB::fetchRow('users', 'id,username,privacy', ['id ='], [$id]);
        $peers = PeersModel::getPeers($type, $id, $this->current_user);

        // Data For View
        $data = [
            'V_id' => $id,
            'page_title' => $peers['title'],
            'V_user' => $user,
            'V_count' => $peers['count'],
            'V_peers' => $peers['stmt'],
            'V_pager' => $peers['pager'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-peer', $data, 'user');
    }
}