<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\PeersLogic;

/*
* Peers Controller
*/
class Adminpeer extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Peers 
    public function index()
    {
        // Show Peers
        $peers = PeersLogic::getPeers('admin', 0, $this->current_user, 'adminpeer?');

        // Data For View
        $data = [
            'TITLE' => LANG["_PEERS"],
            'COUNT' => $peers['count'],
            'PAGER' => $peers['pager'],
            'PEERS' => $peers['stmt']
        ];
        
        // Load View
        $this->template('admin-peer', $data, 'admin');
   }

}