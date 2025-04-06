<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ShoutboxLogic;

/*
* Shoutbox Controller
*/
class Shoutbox extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    public function index(){}

    // Ajax Shoutbox Load
    public function chat()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['SHOUTBOX'],
            'SHOUTS' => ShoutboxLogic::shouts(20),
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->template('user-shoutboxload', $data);
    }

    // Add Shout Submit
    public function add()
    {
        if (!empty($_POST)) {
            ShoutboxLogic::add($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Delete Shout Submit
    public function delete($id, $sure = 0)
    {
        ShoutboxLogic::delete($id, $this->current_user, $sure);
    }

    // Edit Shout Default Page
    public function edit()
    {
        if (!empty($_POST)) {
            ShoutboxLogic::edit($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Shoutbox History Default Page
    public function history()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['HISTORY'],
            'SHOUTS' => ShoutboxLogic::shouts(80)
        ];

        // Load View
        $this->template('user-shoutbox', $data, 'user');
    }

}