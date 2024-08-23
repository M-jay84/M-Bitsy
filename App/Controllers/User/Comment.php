<?php

namespace App\Controllers\User;

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
        $this->current_user = $this->currentuser(2);
    }

    // Comments Default Page
    public function index($id, $type)
    {
    
        // Get Data
        $comments = (new CommentsModel)->getInfo($id, $type);

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
        $this->view('comment', $data, 'user');
    }

    // Add/Edit Comment
    public function submit($targetid, $type)
    {
    
        $data['current_user'] = $this->current_user;
        (new CommentsModel)->action($targetid, $data, $type);
    }

    // Delete Comment Default Page
    public function delete($id, $type = 0)
    {
    
        if (!$this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            $data['current_user'] = $this->current_user;
            (new CommentsModel)->delete($id, $data, $type);
        }
    }

}