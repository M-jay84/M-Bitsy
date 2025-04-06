<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CommentsLogic;

/*
* Comments Controller
*/
class Comment extends Controller
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

    // Comments
    public function index($id, $type)
    {
        // Get Comments
        $comments =  CommentsLogic::getInfo($id, $type);

        // Data For View
        $data = [
            'TITLE' => $comments['title'],
            'ROW' => $comments['row'],
            'COMMENT' => $comments['res'],
            'PAGER' => $comments['pager'],
            'COUNT' => $comments['count'],
            'TYPE' => $type,
            'TARGET_ID' => $id
        ];

        // Load View
        $this->template('user-comment', $data, 'user');
    }

    // Add/Edit Comment
    public function submit($targetid, $type)
    {
        if (!empty($_POST)) {
            CommentsLogic::action($targetid, $this->current_user->id, $type);
        } else {
            showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
    }

    // Delete Comment Default Page
    public function delete($id, $type = 0)
    {
        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        } else {
            CommentsLogic::delete($id, $this->current_user->id, $type);
        }
    }

}