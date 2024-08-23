<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\ShoutboxModel;

/*
* Shoutbox Controller
*/
class Shoutbox extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(0);
    }

    // No Default Page
    public function index()
    {
        Url(CONFIG['URLROOT'], true);
    }

    // Ajax Shoutbox Load
    public function chat()
    {
        $shouts = (new ShoutboxModel)->shouts(20);

        // Data For View
        $data = [
            'page_title' => LANG['SHOUTBOX'],
            'V_shouts' => $shouts,
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('shoutboxload', $data);
    }

    // Add Shout Submit
    public function add()
    {
        (new ShoutboxModel)->add($this->current_user);
    }

    // Delete Shout Submit
    public function delete($id, $sure = 0)
    {
        (new ShoutboxModel)->delete($id, $sure);
    }

    // Edit Shout Default Page
    public function edit()
    {
        (new ShoutboxModel)->edit($this->current_user);
    }

    // Shoutbox History Default Page
    public function history()
    {
        $shouts = (new ShoutboxModel)->shouts(80);
        
        // Data For View
        $data = [
            'page_title' => LANG['HISTORY'],
            'V_shouts' => $shouts
        ];

        // Load View
        $this->view('shoutbox', $data, 'user');
    }

}