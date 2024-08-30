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
        $this->current_user = (new Auth)->user(1);
        if (!CONFIG['FORUMS']) {
            showMessage(Url(), LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->forumbanned == "yes" || $this->current_user->view_forum == "no") {
            showMessage(Url(), LANG["FORUM_BANNED"]);
        }
    }

    // Forum Page
    public function index()
    {
        // Get Forum Data
        $forums = (new ForumsModel)->getCatForums();

        // Data For View
        $data = [
            'page_title' => LANG["FORUM"],
            'V_forums' => $forums
        ];

        // Load View
        $this->view('forum', $data, 'user');
    }

    // Search Forum Page
    public function search()
    {
        $data = (new ForumsModel)->search();
		$data['curremt_user'] = $this->current_user;

        // Load View
        $this->view('forum_search', $data, 'user');
    }

    // View Unread Topics Page
    public function viewunread($catchup = false)
    {
        if ($catchup == true) {
            catch_up();
        }
        
        // Get Data
        $data['current_user'] = $this->current_user;
        $forums = (new ForumsModel)->unread($data);
        
		// Data For View
        $data = [
            'V_forums' => $forums,
            'page_title' => LANG["Forums"],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('forum_unread', $data, 'user');
    }

}