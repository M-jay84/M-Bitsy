<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\MessagesLogic;

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
            MessagesLogic::multiPM($this->current_user->id);
        } else {
            // Get Messages
            $messages = MessagesLogic::getAll();
       
            // Data For View
            $data = [
                'TITLE' => LANG["MESSAGE"],
                'MESSAGES' => $messages['res'],
                'PAGER' => $messages['pager'],
                'GROUPS' => $messages['groups'],
                'USER' => $this->current_user
            ];
       
            // Load View
            $this->template('admin-message', $data, 'admin');
        }
    }

    // Message Form Submit
    public function delete()
    {
        if (!empty($_POST)) {
            MessagesLogic::deleteByIds();
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}