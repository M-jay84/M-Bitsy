<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\CommentsModel;

/*
* Comments Controller
*/
class Comment 
{
    // Comments Default Page
    public function index($id, $type)
    {
        $USER = new User(0, 2);
        // Get Data
        $comments = CommentsModel::getInfo($id, $type);

        // Init Data
        $data = [
            'V_title' => $comments['title'],
            'V_row' => $comments['row'],
            'V_comments' => $comments['res'],
            'V_pager' => $comments['pager'],
            'V_count' => $comments['count'],
            'V_type' => $type,
            'V_targetid' => $id,
            'USER' => $USER
        ];

        // Load View
        template('comment', $data, 'user');
    }

    // Add/Edit Comment
    public function submit($targetid, $type)
    {
        $USER = new User(0, 2);
        CommentsModel::action($targetid, $type);
    }

    // Delete Comment Default Page
    public function delete($id, $type = 0)
    {
        $USER = new User(0, 2);
        if (!$USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            CommentsModel::delete($id, $type);
        }
    }

}