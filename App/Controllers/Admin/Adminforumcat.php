<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\ForumsModel;

/*
* forum Cats Controller
*/
class AdminforumCat 
{
    // Forum Cat Default Page
    public function index($id = 0, $move = false)
    {
        $USER = new User(_MODERATOR, 2);
        if ($move) {
            ForumsModel::reorder($id, $move);
        }

        // Get Forum Cat Data
        $forumcat = ForumsModel::getCats();

        // Init Data
        $data = [
            'V_title' => Txt("FORUMS"),
            'V_category' => $forumcat,
            'USER' => $USER
        ];

        // Load View
        template('forumcat', $data, 'admin');
    }

    // Add Forum Cat Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        ForumsModel::insertOrUpdate();
    }

    // Delete Forum Cat Submit
    public function delete($id, $action = '')
    {
        $USER = new User(_MODERATOR, 2);
        ForumsModel::deleteCats($id, $action);
    }

}