<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\StaffMessagesModel;

/*
* Contact Controller
*/
class Admincontact 
{
    // Contact Staff Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            StaffMessagesModel::update();
        } else {
            // Get Contact Staff Data
            $messages = StaffMessagesModel::select();
       
            // Init Data
            $data = [
                'V_title' => Txt('STAFF_PMS'),
                'V_messages' => $messages,
                'USER' => $USER
            ];
       
            // Load View
            template('contact', $data, 'admin');
        }
    }

    // Delete Message Default Page
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        StaffMessagesModel::delete();
    }

}