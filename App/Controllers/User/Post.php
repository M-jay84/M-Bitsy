<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsLogic;
use App\Models\PostsLogic;

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
            showMessage(URLROOT, LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->forumbanned == "yes" || $this->current_user->view_forum == "no") {
            showMessage(URLROOT, LANG["FORUM_BANNED"]);
        }
    }

    // View Forum Posts
    public function index($topicid)
    {
        // Get Posts
        $post = PostsLogic::getTopicPages($topicid, $this->current_user);

        // Data For View
        $data = [
            'TITLE' => $post['subject'],
            'POSTS' => $post['stmt'],
            'TOPICID' => $topicid,
            'INFO' => $post,
            'PAGER' => $post["pager"],
            'USER' => $this->current_user
        ];

        // Load View
        $this->template('user-post', $data, 'user');
    }

    // Add Post
    public function submit($topicid) // todo
    {
        if (!empty($_POST)) {
            PostsLogic::addPost($topicid, $this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // Post Edit Form Submit
    public function edit()
    {
        if (!empty($_POST)) {
            PostsLogic::edit($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }
    
    // Post Delete Submit
    public function delete($postid, $sure = 0)
    {
        PostsLogic::deletePost($postid, $this->current_user, $sure);
    }

    // Users Posts Default Page
    public function user($id)
    {
        // Get Users Posts
        $posts = ForumsLogic::getUsersPost($id, $this->current_user);

        // Data For View To View
        $data = [
            'TITLE' => LANG["USER_POSTS"],
            'ID' => $id,
            'POSTS' => $posts['row'],
            'PAGER' => $posts['pager'],
            'USER' => $this->current_user
        ];

        // Load View
        $this->template('user-post-user', $data, 'user');
    }
    
}