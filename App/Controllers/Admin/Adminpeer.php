<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\PeersModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Peers Default Page
    public function index()
    {
        // Get Data
        $peers = PeersModel::getPeers('admin', 0, $this->current_user, 'adminpeer?');

        // Data For View
        $data = [
            'page_title' => LANG["_PEERS"],
            'V_count' => $peers['count'],
            'V_pager' => $peers['pager'],
            'V_peers' => $peers['stmt']
        ];
        
        // Load View
        $this->view('admin-peer', $data, 'admin');
   }

}