<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\CommentsModel;

/*
* Comment Controller
*/
class Admincomment 
{
    // Comments Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Data
        $comments = CommentsModel::grabAll();
        
        // Init Data
        $data = [
            'V_title' => Txt("COMMENT"),
            'V_comments' => $comments['stmt'],
            'V_pager' => $comments['pager'],
            'USER' => $USER
        ];

        // Load View
        template('comment', $data, 'admin');
    }

}