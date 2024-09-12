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

        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Message Default Page
    public function index()
    {
        if (!empty($_POST)) {
            MessagesModel::multiPM($this->current_user->id);
        } else {
            // Get Messages
            $messages = MessagesModel::getAll();
       
            // Data For View
            $data = [
                'page_title' => LANG["MESSAGE"],
                'V_messages' => $messages['res'],
                'V_pager' => $messages['pager'],
                'V_groups' => $messages['groups'],
                'current_user' => $this->current_user
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