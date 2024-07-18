<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\StaffMessagesModel;

/*
* Confirm Controller
*/
class Contact 
{
    // Contact Us Page
    public function index()
    {
        $USER = new User(0, 1);
        // Init Data
        $data = [
            'V_title' => 'Contact Staff',
            'USER' => $USER
        ];

        // Load View
        template('contact', $data, 'user');
    }

    // Contact Form Submit
    public function submit()
    {
        $USER = new User(0, 1);
        if (!empty($_POST)) {
            StaffMessagesModel::insert();
        } else {
            Redirect(Url('bonus'), Txt("NOTHING_FOUND"));
        }
    }
}