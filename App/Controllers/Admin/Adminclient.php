<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\ClientsModel;

/*
* Client Controller
*/
class Adminclient 
{
    // Client Default Page
    public function index($type = 'peers')
    {
        $USER = new User(_MODERATOR, 2);
        // Get Peer Client Data
        $client = ClientsModel::select($type);

        // Init Data
        $data = [
            'V_title' => Txt("CLIENTS"),
            'V_client' => $client,
            'V_type' => $type,
            'USER' => $USER
        ];
        
        // Load View
        template('client', $data, 'admin');
    }

    // Add Client Record
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        ClientsModel::insert();
    }

    // Delete Client Record
    public function delete($id)
    {
        $USER = new User(_MODERATOR, 2);
        ClientsModel::delete($id);
    }

}