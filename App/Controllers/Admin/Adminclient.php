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
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Client Default Page
    public function index($type = 'peers')
    {
    
        // Get Peer Client Data
        $client = (new ClientsModel)->select($type);

        // Data For View
        $data = [
            'page_title' => LANG["CLIENTS"],
            'V_client' => $client,
            'V_type' => $type
        ];
        
        // Load View
        $this->view('client', $data, 'admin');
    }

    // Add Client Record
    public function submit()
    {
    
        (new ClientsModel)->insert();
    }

    // Delete Client Record
    public function delete($id)
    {
    
        (new ClientsModel)->delete($id);
    }

}