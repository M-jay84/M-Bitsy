<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\StaffMessagesModel;

/*
* Contact Controller
*/
class Admincontact extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Contact Staff Default Page
    public function index()
    {
    
        if (!empty($_POST)) {
            (new StaffMessagesModel)->update($this->current_user->id);
        } else {
            // Get Contact Staff Data
            $messages = (new StaffMessagesModel)->select();
       
            // Data For View
            $data = [
                'page_title' => LANG['STAFF_PMS'],
                'V_messages' => $messages
            ];
       
            // Load View
            $this->view('contact', $data, 'admin');
        }
    }

    // Delete Message Default Page
    public function delete()
    {
    
        (new StaffMessagesModel)->delete();
    }

}