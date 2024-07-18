<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\MessagesModel;
use App\Models\UsersModel;

/*
* Messages Controller
*/
class Message 
{
    // Message Default Page
    public function index($id, $type = 'inbox')
    {
        $USER = new User(0, 2);
        // Set Button
        if ($type == 'templates' || $type == 'draft') {
            $button = "<a href='javascript:;' class='addAttr' data-bs-toggle='modal' data-bs-target='#editModal' '><button class='btn ttbtn m-2'><i class='fa fa-home me-2'></i>Edit</button></a>";
        } elseif ($type == 'inbox' || $type == 'outbox') {
            $button = " <a href='javascript:;' class='addAttr' data-bs-toggle='modal' data-bs-target='#replyModal' '><button class='btn ttbtn m-2'><i class='fa fa-home me-2'></i>Reply</button></a>";
        }
        
        // Get Message
        $messages = MessagesModel::getById($id);
        $templates = MessagesModel::echoTemplates();

        // Init Dta
        $data = [
            'V_title' => Txt('MESSAGE'),
            'V_button' => $button,
            'V_templates' => $templates,
            'V_messages' => $messages,
            'USER' => $USER
        ];

        // Load View
        template('message', $data, 'user');
    }

    // Mailbox Default Page
    public function mailbox($type = 'inbox')
    {
        $USER = new User(0, 2);
        // Count All Messages
        $count = MessagesModel::countMsg();

        // Pagination
        $messages = MessagesModel::msgPagination($type);
        
        // Init Dta
        $data = [
            'V_title' => $type,
            'V_type' => $type,
            'V_messages' => $messages['messages'],
            'V_count' => $count,
            'V_pager' => $messages['pager'],
            'USER' => $USER
        ];

        // Load View
        template('mailbox', $data, 'user');
    }

    // Ajax User search
	function findUser()
    {
        $USER = new User(0, 2);
        if(!empty($_POST["keyword"])) {
            $result = UsersModel::getUserName();
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

    // Create Message Default Page
    public function create($id = 0)
    {
        $USER = new User(0, 2);
        $templates = MessagesModel::echoTemplates();

        // Init Dta
        $data = [
            'V_title' => Txt('MESSAGE'),
            'V_id' => $id,
            'V_templates' => $templates,
            'USER' => $USER
        ];

        // Load View
        template('message_create', $data, 'user');
    }

    // Create/Reply Message Form Submit
    public function submit()
    {
        $USER = new User(0, 2);
        MessagesModel::insertByType();
    }

    // Message Edit Default Page
    public function update($id)
    {
        $USER = new User(0, 2);
        MessagesModel::update($id);
    }

    // Delete/MarkRead Message Submit
    public function delete($type)
    {
        $USER = new User(0, 2);
        MessagesModel::delete($type);
    }
}