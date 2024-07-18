<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\PollsModel;

/*
* Polls Controller
*/
class Adminpoll 
{
    // Polls Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Poll Data
        $polls = PollsModel::getAll();

        // Init Data
        $data = [
            'V_title' => Txt("POLLS"),
            'V_polls' => $polls,
            'USER' => $USER
        ];

        // Load View
        template('poll', $data, 'admin');
    }

    // Add/Edit Poll Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        // Add Insert / Edit Update
        PollsModel::insertOrUpdate();
    }

    // Delete Poll Form Submit
    public function delete($id)
    {
        $USER = new User(_MODERATOR, 2);
        PollsModel::delete($id);
    }

}