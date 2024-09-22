<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsLogic;

/*
* Forum Controller
*/
class Forum extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!CONFIG['FORUMS']) {
            showMessage(URLROOT, LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->forumbanned == "yes" || $this->current_user->view_forum == "no") {
            showMessage(URLROOT, LANG["FORUM_BANNED"]);
        }
    }

    // Forums
    public function index()
    {
        // Data For View
        $data = [
            'TITLE' => LANG["FORUM"],
            'FORUMS' => ForumsLogic::getCatForums()
        ];

        // Load View
        $this->template('user-forum', $data, 'user');
    }

    // Search Forum
    public function search()
    {
        // Search Forum
        $data = ForumsLogic::search();
		$data['USER'] = $this->current_user;

        // Load View
        $this->template('user-forum-search', $data, 'user');
    }

    // View Unread Topics Page
    public function viewunread($catchup = false)
    {
        if ($catchup == true) {
            catch_up();
        }
        
        // Get Data
        $forums = ForumsLogic::unread($this->current_user);
        
		// Data For View
        $data = [
            'FORUMS' => $forums,
            'TITLE' => LANG["Forums"],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('user-forum-unread', $data, 'user');
    }
}