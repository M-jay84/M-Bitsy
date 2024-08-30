<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsModel;

/*
* Forum Controller
*/
class Adminforum extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Forum Default Page
    public function index($id, $forumid = 0, $move = false)
    {
    
        if ($move) {
            (new ForumsModel)->reorderForum($id, $forumid, $move);
        }

        // Get Forum Data
        $forum = (new ForumsModel)->getForums($id);

        // Data For View
        $data = [
            'page_title' => LANG["FORUMS"],
            'V_forumcat' => $forum['forumcat'],
            'V_forumforum' => $forum['forumforum'],
            'V_query' => $forum['query'],
            'V_name' => $forum['name'],
            'V_groups' => $forum['groups']
        ];
        
        // Load View
        $this->view('forum', $data, 'admin');
    }

    // Add Forum Submit
    public function submit()
    {
    
        (new ForumsModel)->insertOrUpdateForum();
    }

    // Delete Forum Submit
    public function delete($id, $sure = '')
    {
    
        (new ForumsModel)->delete($id, $sure);
    }

}