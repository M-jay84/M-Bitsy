<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\ShoutboxModel;

/*
* Shoutbox Controller
*/
class Adminshoutbox 
{
    // Shoutbox Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Init Data
        $data = [
            'V_title' => Txt('SHOUTBOX'),
            'USER' => $USER
        ];

        // Load View
        template('shoutbox', $data, 'admin');
    }

    // Ajax Shoutbox Load
    public function loadchat()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Shoutbox Data
        $shouts = ShoutboxModel::shouts(20, 1);

        // Init Data
        $data = [
            'V_title' => Txt('SHOUTBOX'),
            'V_shouts' => $shouts,
            'USER' => $USER
        ];
        // Load View
        template('shoutboxload', $data, 'home');
    }

    // Shoutbox History Default Page
    public function history()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Shoutbox Data
        $shouts = ShoutboxModel::shouts(80, 1);

        // Init Data
        $data = [
            'V_title' => Txt('HISTORY'),
            'V_shouts' => $shouts,
            'USER' => $USER
        ];

        // Load View
        template('shoutbox_history', $data, 'admin');
    }

    // Add Shout Form Submit
    public function add()
    {
        $USER = new User(_MODERATOR, 2);
        ShoutboxModel::add($USER, 1);
    }

    // Clear Shoutbox Default Page
    public function clear($sure = '')
    {
        $USER = new User(_MODERATOR, 2);
        ShoutboxModel::truncate($sure);
    }

}