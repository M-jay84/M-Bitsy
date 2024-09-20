<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\MessagesModel;

/*
* Message Controller
*/
class Adminmessage extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->class > 6) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Messages
    public function index()
    {
        if (!empty($_POST)) {
            // Send Multiple PMs
            MessagesModel::multiPM($this->current_user->id);
        } else {
            // Get Messages
            $messages = MessagesModel::getAll();
       
            // Data For View
            $data = [
                'TITLE' => LANG["MESSAGE"],
                'MESSAGES' => $messages['res'],
                'PAGER' => $messages['pager'],
                'GROUPS' => $messages['groups'],
                'USER' => $this->current_user
            ];
       
            // Load View
            $this->view('admin-message', $data, 'admin');
        }
    }

    // Message Form Submit
    public function delete()
    {
        MessagesModel::deleteByIds();
    }

}