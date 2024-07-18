<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\PostsModel;
use App\Models\ForumsModel;

/*
* Post Controller
*/
class Post 
{
    // View Forum Topic Default Page
    public function index($topicid)
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        $post = PostsModel::getTopicPages($topicid);

        // Init Data
        $data = [
            'V_title' => $post['subject'],
            'V_posts' => $post['stmt'],
            'V_topicid' => $topicid,
            'V_info' => $post,
            'V_pager' => $post["pager"],
            'USER' => $USER
        ];

        // Load View
        template('post', $data, 'user');
    }

    public function submit($topicid) // todo
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        PostsModel::addPost($topicid);
    }

    // Post Edit Form Submit
    public function edit()
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        if (!empty($_POST)) {
            PostsModel::edit($USER);
        } else {
            Redirect(Url(), Txt("NOTHING_SELECTED"));
        }
        
    }
    
    // Post Delete Submit
    public function delete($postid, $sure = 0)
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        PostsModel::deletePost($USER, $postid, $sure);
    }

    // Users Posts Default Page
    public function user($id)
    {
        $USER = new User(0, 1);
        $USER->permission('forum');
        $posts = ForumsModel::getUsersPost($USER, $id);

        // Init Data To View
        $data = [
            'V_title' => Txt("USER_POSTS"),
            'V_id' => $id,
            'V_posts' => $posts['row'],
            'V_pager' => $posts['pager'],
            'USER' => $USER
        ];

        // Load View
        template('post_user', $data, 'user');
    }
    
}