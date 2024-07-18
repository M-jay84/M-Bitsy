<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\ShoutboxModel;

/*
* Shoutbox Controller
*/
class Shoutbox 
{
    // No Default Page
    public function index()
    {
        Redirect(URLROOT);
    }

    // Ajax Shoutbox Load
    public function chat()
    {
        $USER = new User(0, 0);
        $shouts = ShoutboxModel::shouts(20);

        // Init Data
        $data = [
            'V_title' => Txt('SHOUTBOX'),
            'V_shouts' => $shouts,
            'USER' => $USER
        ];
        
        // Load View
        template('shoutboxload', $data, 'home');
    }

    // Add Shout Submit
    public function add()
    {
        $USER = new User(0, 0);
        ShoutboxModel::add($USER);
    }

    // Delete Shout Submit
    public function delete($id, $sure = 0)
    {
        $USER = new User(0, 0);
        ShoutboxModel::delete($USER, $id, $sure);
    }

    // Edit Shout Default Page
    public function edit()
    {
        $USER = new User(0, 0);
        ShoutboxModel::edit();
    }

    // Shoutbox History Default Page
    public function history()
    {
        $USER = new User(0, 0);
        $shouts = ShoutboxModel::shouts(80);
        
        // Init Data
        $data = [
            'V_title' => Txt('HISTORY'),
            'V_shouts' => $shouts,
            'USER' => $USER
        ];

        // Load View
        template('shoutbox', $data, 'user');
    }

}