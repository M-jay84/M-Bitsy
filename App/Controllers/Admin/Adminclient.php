<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ClientsLogic;

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
        $client = ClientsLogic::select($type);

        // Data For View
        $data = [
            'TITLE' => LANG["CLIENTS"],
            'CLIENTS' => $client,
            'TYPE' => $type
        ];
        
        // Load View
        $this->template('admin-client', $data, 'admin');
    }

    // Add Client Record
    public function submit()
    {
        if (!empty($_POST)) {
            // Add/Edit Catagories
            ClientsLogic::insert();
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Delete Client Record
    public function delete($id)
    {
        ClientsLogic::delete($id);
    }

}