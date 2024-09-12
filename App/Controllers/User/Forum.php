<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsModel;

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
            $this->showMessage(URLROOT, LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->forumbanned == "yes" || $this->current_user->view_forum == "no") {
            $this->showMessage(URLROOT, LANG["FORUM_BANNED"]);
        }
    }

    // Forum Page
    public function index()
    {
        // Data For View
        $data = [
            'page_title' => LANG["FORUM"],
            'V_forums' => ForumsModel::getCatForums()
        ];

        // Load View
        $this->view('user-forum', $data, 'user');
    }

    // Search Forum Page
    public function search()
    {
        $data = ForumsModel::search();
		$data['curremt_user'] = $this->current_user;

        // Load View
        $this->view('user-forum-search', $data, 'user');
    }

    // View Unread Topics Page
    public function viewunread($catchup = false)
    {
        if ($catchup == true) {
            catch_up();
        }
        
        // Get Data
        $forums = ForumsModel::unread($this->current_user);
        
		// Data For View
        $data = [
            'V_forums' => $forums,
            'page_title' => LANG["Forums"],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-forum-unread', $data, 'user');
    }

}