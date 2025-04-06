<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\MessagesLogic;
use App\Models\UsersLogic;

/*
* Messages Controller
*/
class Message extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Messages
    public function index($id, $type = 'inbox')
    {
        // Get Message
        $messages = MessagesLogic::getById($id, $this->current_user->id);

        // Init Dta
        $data = [
            'TITLE' => LANG['MESSAGE'],
            'MESSAGES' => $messages,
            'USERS' => $this->current_user,
            'TYPE' => $type
        ];

        // Load View
        $this->template('user-message', $data, 'user');
    }

    // Mailbox
    public function mailbox($type = 'inbox')
    {
        // Get All Messages
        $messages = MessagesLogic::msgPagination($type, $this->current_user->id);
        
        // Init Dta
        $data = [
            'TITLE' => $type,
            'TYPE' => $type,
            'MESSAGES' => $messages['messages'],
            'PAGER' => $messages['pager']
        ];

        // Load View
        $this->template('user-message-mailbox', $data, 'user');
    }

    // Ajax User search
	function findUser()
    {
        if(!empty($_POST)) {
            $result = UsersLogic::getUserName();
            if(!empty($result)) {
                ?>
                <ul id="country-list">
                <?php foreach($result as $user) { ?>
                    <li onClick="userCountry('<?php echo $user["username"]; ?>');"><?php echo $user["username"]; ?></li>
                <?php } ?>
                </ul>
                <?php
            }
        }
    }

    // Create Message
    public function create($id = 0)
    {
        // Init Dta
        $data = [
            'TITLE' => LANG['MESSAGE'],
            'ID' => $id,
            'USER' => $this->current_user
        ];

        // Load View
        $this->template('user-message-create', $data, 'user');
    }

    // Create/Reply Message Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            MessagesLogic::insertByType($this->current_user->id);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Message Edit
    public function update($id)
    {
        if (!empty($_POST)) {
            MessagesLogic::update($id, $this->current_user->id);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Delete/MarkRead Message Submit
    public function delete($type = 0)
    {
        if (!empty($_POST)) {
            MessagesLogic::delete($type, $this->current_user->id);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

}