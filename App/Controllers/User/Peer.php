<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\PeersLogic;
use PDO;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Peers
    public function index($type, $id)
    {
        // Get Peers & Users
        $user_query = "SELECT id,username,privacy FROM `users` WHERE id = ?";
        $user =  DB::run($user_query, [$id])->fetch(PDO::FETCH_ASSOC);
        $peers = PeersLogic::getPeers($type, $id, $this->current_user);

        // Data For View
        $data = [
            'ID' => $id,
            'TITLE' => $peers['title'],
            'USER' => $user,
            'COUNT' => $peers['count'],
            'PEERS' => $peers['stmt'],
            'PAGER' => $peers['pager'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('user-peer', $data, 'user');
    }
}