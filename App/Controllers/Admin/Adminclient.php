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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Client Page
    public function index($type = 'peers')
    {
        // Get Client Data
        $client = ClientsModel::select($type);

        // Data For View
        $data = [
            'TITLE' => LANG["CLIENTS"],
            'CLIENTS' => $client,
            'TYPE' => $type
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