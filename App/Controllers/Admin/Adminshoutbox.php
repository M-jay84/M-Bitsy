<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ShoutboxModel;

/*
* Shoutbox Controller
*/
class Adminshoutbox extends Controller
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

    // Shoutbox Default Page
    public function index()
    {
    
        // Data For View
        $data = [
            'page_title' => LANG['SHOUTBOX']
        ];

        // Load View
        $this->view('shoutbox', $data, 'admin');
    }

    // Ajax Shoutbox Load
    public function loadchat()
    {
    
        // Get Shoutbox Data
        $shouts = (new ShoutboxModel)->shouts(20, 1);

        // Data For View
        $data = [
            'page_title' => LANG['SHOUTBOX'],
            'V_shouts' => $shouts,
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('shoutboxload', $data);
    }

    // Shoutbox History Default Page
    public function history()
    {
    
        // Get Shoutbox Data
        $shouts = (new ShoutboxModel)->shouts(80, 1);

        // Data For View
        $data = [
            'page_title' => LANG['HISTORY'],
            'V_shouts' => $shouts,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('shoutbox_history', $data, 'admin');
    }

    // Add Shout Form Submit
    public function add()
    {
    
        (new ShoutboxModel)->add(1);
    }

    // Clear Shoutbox Default Page
    public function clear($sure = '')
    {
    
        (new ShoutboxModel)->truncate($sure);
    }

}