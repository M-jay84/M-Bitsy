<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\MessagesModel;
use App\Models\UsersModel;

/*
* Messages Controller
*/
class Message extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Message Default Page
    public function index($id, $type = 'inbox')
    {
    
        // Set Button
        if ($type == 'templates' || $type == 'draft') {
            $button = "<a href='javascript:;' class='addAttr' data-bs-toggle='modal' data-bs-target='#editModal' '><button class='btn ttbtn m-2'><i class='fa fa-home me-2'></i>Edit</button></a>";
        } elseif ($type == 'inbox' || $type == 'outbox') {
            $button = " <a href='javascript:;' class='addAttr' data-bs-toggle='modal' data-bs-target='#replyModal' '><button class='btn ttbtn m-2'><i class='fa fa-home me-2'></i>Reply</button></a>";
        }
        
        // Get Message
        $messages = (new MessagesModel)->getById($id, $this->current_user->id);
        $templates = (new MessagesModel)->echoTemplates($this->current_user->id);

        // Init Dta
        $data = [
            'page_title' => LANG['MESSAGE'],
            'V_button' => $button,
            'V_templates' => $templates,
            'V_messages' => $messages,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('message', $data, 'user');
    }

    // Mailbox Default Page
    public function mailbox($type = 'inbox')
    {
    
        $data['current_user'] = $this->current_user;
        // Count All Messages
        $count = (new MessagesModel)->countMsg($data);

        // Pagination
        $messages = (new MessagesModel)->msgPagination($type, $this->current_user->id);
        
        // Init Dta
        $data = [
            'page_title' => $type,
            'V_type' => $type,
            'V_messages' => $messages['messages'],
            'V_count' => $count,
            'V_pager' => $messages['pager'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('mailbox', $data, 'user');
    }

    // Ajax User search
	function findUser()
    {
    
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
    
        $templates = (new MessagesModel)->echoTemplates($this->current_user->id);

        // Init Dta
        $data = [
            'page_title' => LANG['MESSAGE'],
            'V_id' => $id,
            'V_templates' => $templates,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('message_create', $data, 'user');
    }

    // Create/Reply Message Form Submit
    public function submit()
    {
    
        (new MessagesModel)->insertByType($this->current_user->id);
    }

    // Message Edit Default Page
    public function update($id)
    {
    
        (new MessagesModel)->update($id, $this->current_user->id);
    }

    // Delete/MarkRead Message Submit
    public function delete($type)
    {
    
        (new MessagesModel)->delete($type, $this->current_user->id);
    }
}