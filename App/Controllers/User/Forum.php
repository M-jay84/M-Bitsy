<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\ForumsModel;

/*
* Forum Controller
*/
class Forum 
{
    // Forum Page
    public function index()
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        // Get Forum Data
        $forums = ForumsModel::getCatForums();

        // Init Data
        $data = [
            'V_title' => Txt("FORUM"),
            'V_forums' => $forums,
            'USER' => $USER
        ];

        // Load View
        template('forum', $data, 'user');
    }

    // Search Forum Page
    public function search()
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        $data = ForumsModel::search();
        $data['USER'] = $USER;

        // Load View
        template('forum_search', $data, 'user');
    }

    // View Unread Topics Page
    public function viewunread($catchup = false)
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        if ($catchup == true) {
            catch_up();
        }
        
        // Get Data
        $forums = ForumsModel::unread();
        
		// Init Data
        $data = [
            'V_forums' => $forums,
            'V_title' => Txt("Forums"),
            'USER' => $USER
        ];

        // Load View
        template('forum_unread', $data, 'user');
    }

}