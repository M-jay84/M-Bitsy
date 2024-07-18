<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\ForumsModel;

/*
* Forum Controller
*/
class Adminforum 
{
    // Forum Default Page
    public function index($id, $forumid = 0, $move = false)
    {
        $USER = new User(_MODERATOR, 2);
        if ($move) {
            ForumsModel::reorderForum($id, $forumid, $move);
        }

        // Get Forum Data
        $forum = ForumsModel::getForums($id);

        // Init Data
        $data = [
            'V_title' => Txt("FORUMS"),
            'V_forumcat' => $forum['forumcat'],
            'V_forumforum' => $forum['forumforum'],
            'V_query' => $forum['query'],
            'V_name' => $forum['name'],
            'V_groups' => $forum['groups'],
            'USER' => $USER
        ];
        
        // Load View
        template('forum', $data, 'admin');
    }

    // Add Forum Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        ForumsModel::insertOrUpdateForum();
    }

    // Delete Forum Submit
    public function delete($id, $sure = '')
    {
        $USER = new User(_MODERATOR, 2);
        ForumsModel::delete($id, $sure);
    }

}