<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\PostsModel;
use App\Models\ForumsModel;

/*
* Post Controller
*/
class Post extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!CONFIG['FORUMS']) {
            $this-> $this->showMessage(URLROOT, LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->forumbanned == "yes" || $this->current_user->view_forum == "no") {
            $this->showMessage(URLROOT, LANG["FORUM_BANNED"]);
        }
    }

    // View Forum Topic Default Page
    public function index($topicid)
    {
        $post = PostsModel::getTopicPages($topicid, $this->current_user);

        // Data For View
        $data = [
            'page_title' => $post['subject'],
            'V_posts' => $post['stmt'],
            'V_topicid' => $topicid,
            'V_info' => $post,
            'V_pager' => $post["pager"],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-post', $data, 'user');
    }

    public function submit($topicid) // todo
    {
        if (!empty($_POST)) {
            PostsModel::addPost($topicid, $this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Post Edit Form Submit
    public function edit()
    {
        if (!empty($_POST)) {
            PostsModel::edit($this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }
    
    // Post Delete Submit
    public function delete($postid, $sure = 0)
    {
        PostsModel::deletePost($postid, $this->current_user, $sure);
    }

    // Users Posts Default Page
    public function user($id)
    {
        $posts = ForumsModel::getUsersPost($id, $this->current_user);

        // Data For View To View
        $data = [
            'page_title' => LANG["USER_POSTS"],
            'V_id' => $id,
            'V_posts' => $posts['row'],
            'V_pager' => $posts['pager'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('user-post-user', $data, 'user');
    }
    
}