<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\PeersModel;

/*
* Peers Controller
*/
class Adminpeer 
{
    // Peers Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Data
        $peers = PeersModel::getPeers($USER, 'admin', $id, 'adminpeer?');

        // Init Data
        $data = [
            'V_title' => Txt("_PEERS"),
            'V_count' => $peers['count'],
            'V_pager' => $peers['pager'],
            'V_peers' => $peers['stmt'],
            'USER' => $USER
        ];
        
        // Load View
        template('peer', $data, 'admin');
   }

}