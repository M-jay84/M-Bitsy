<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\PeersModel;
use App\Models\UsersModel;

/*
* Peers Controller
*/
class Peer 
{
    // No Default Page // todo
    public function index($type, $id)
    {
        $USER = new User(0, 2);
        // Check User Input
        $user =  UsersModel::fetchRow('id,username,privacy', ['id ='], [$id]);
        $peers = PeersModel::getPeers($USER, $type, $id);

        // Init Data
        $data = [
            'V_id' => $id,
            'V_title' => $peers['title'],
            'V_user' => $user,
            'V_count' => $peers['count'],
            'V_peers' => $peers['stmt'],
            'V_pager' => $peers['pager'],
            'USER' => $USER
        ];

        // Load View
        template('peer', $data, 'user');
    }
}