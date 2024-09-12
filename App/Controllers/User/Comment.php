<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CommentsModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Comments Default Page
    public function index($id, $type)
    {
        // Get Data
        $comments =  CommentsModel::getInfo($id, $type);

        // Data For View
        $data = [
            'page_title' => $comments['title'],
            'V_row' => $comments['row'],
            'V_comments' => $comments['res'],
            'V_pager' => $comments['pager'],
            'V_count' => $comments['count'],
            'V_type' => $type,
            'V_targetid' => $id
        ];

        // Load View
        $this->view('user-comment', $data, 'user');
    }

    // Add/Edit Comment
    public function submit($targetid, $type)
    {
        if (!empty($_POST)) {
            CommentsModel::action($targetid, $this->current_user->id, $type);
        } else {
            $this->showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
    }

    // Delete Comment Default Page
    public function delete($id, $type = 0)
    {
        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        } else {
            CommentsModel::delete($id, $this->current_user->id, $type);
        }
    }

}