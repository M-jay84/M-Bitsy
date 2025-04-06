<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsLogic;

/*
* Forum Controller
*/
class Adminforum extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Forum Index
    public function index($id, $forumid = 0, $move = false)
    {
        if ($move) {
            // Sort/mMove Order
            ForumsLogic::reorderForum($id, $forumid, $move);
        }

        // Get Forum Data
        $forum = ForumsLogic::getForums($id);

        // Data For View
        $data = [
            'TITLE' => LANG["FORUMS"],
            'CATS' => $forum['forumcat'],
            'FORUM' => $forum['forumforum'],
            'STMT' => $forum['query'],
            'NAME' => $forum['name'],
            'GROUPS' => $forum['groups']
        ];
        
        // Load View
        $this->template('admin-forum', $data, 'admin');
    }

    // Add/Edit Forum
    public function submit()
    {
        if (!empty($_POST)) {
            ForumsLogic::insertOrUpdateForum();
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Delete Forum
    public function delete($id, $sure = '')
    {
        ForumsLogic::delete($id, $sure);
    }

}