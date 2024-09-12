<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ClientsModel;

/*
* Client Controller
*/
class Adminclient extends Controller
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

    // Client Default Page
    public function index($type = 'peers')
    {
        // Get Peer Client Data
        $client = ClientsModel::select($type);

        // Data For View
        $data = [
            'page_title' => LANG["CLIENTS"],
            'V_client' => $client,
            'V_type' => $type
        ];
        
        // Load View
        $this->view('admin-client', $data, 'admin');
    }

    // Add Client Record
    public function submit()
    {
        ClientsModel::insert();
    }

    // Delete Client Record
    public function delete($id)
    {
        ClientsModel::delete($id);
    }

}