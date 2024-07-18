<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\MessagesModel;

/*
* Message Controller
*/
class Adminmessage 
{
    // Message Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            MessagesModel::multiPM();
        } else {
            // Get Messages
            $messages = MessagesModel::getAll();
       
            // Init Data
            $data = [
                'V_title' => Txt("MESSAGE"),
                'V_messages' => $messages['res'],
                'V_pager' => $messages['pager'],
                'V_groups' => $messages['groups'],
                'USER' => $USER
            ];
       
            // Load View
            template('message', $data, 'admin');
        }
    }

    // Message Form Submit
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        MessagesModel::deleteByIds();
    }

}