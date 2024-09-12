<?php

namespace App\Controllers\User;

use App\Libs\Auth;
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
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
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
        $messages = MessagesModel::getById($id, $this->current_user->id);
        $templates = MessagesModel::echoTemplates($this->current_user->id);

        // Init Dta
        $data = [
            'page_title' => LANG['MESSAGE'],
            'V_button' => $button,
            'V_templates' => $templates,
            'V_messages' => $messages,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-message', $data, 'user');
    }

    // Mailbox Default Page
    public function mailbox($type = 'inbox')
    {
        // Count All Messages
        $data['current_user'] = $this->current_user;
        $count = MessagesModel::countMsg($data);

        // Pagination
        $messages = MessagesModel::msgPagination($type, $this->current_user->id);
        
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
        $this->view('user-mailbox', $data, 'user');
    }

    // Ajax User search
	function findUser()
    {
        if(!empty($_POST)) {
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
        $templates = MessagesModel::echoTemplates($this->current_user->id);

        // Init Dta
        $data = [
            'page_title' => LANG['MESSAGE'],
            'V_id' => $id,
            'V_templates' => $templates,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-message-create', $data, 'user');
    }

    // Create/Reply Message Form Submit
    public function submit()
    {
        MessagesModel::insertByType($this->current_user->id);
    }

    // Message Edit Default Page
    public function update($id)
    {
        MessagesModel::update($id, $this->current_user->id);
    }

    // Delete/MarkRead Message Submit
    public function delete($type)
    {
        MessagesModel::delete($type, $this->current_user->id);
    }

}