<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TopicsModel;

/*
* Topic Controller
*/
class Topic extends Controller
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
        if ($this->current_user->view_forum != "yes" && $this->current_user->class <= CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["FORUM_BANNED"]);
        }
    }

    // View Forum Topic Page
    public function index($forumid)
    {
        // Get Topics Data
        $topics = (new TopicsModel)->getForumTopics($forumid, $this->current_user);

        // Data For View
        $data = [
            'page_title' => LANG["FORUM"],
            'V_topics' => $topics,
            'V_forumid' => $forumid,
            'V_topic_name' => $topics['topic_name'],
            'V_forum_name' => $topics['forum_name'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('topic', $data, 'user');
    }

    // Post Topic
    public function submit($forumid)
    {
        $data['current_user'] = $this->current_user;
        (new TopicsModel)->insertTopic($forumid, $data);
    }

    // Delete a Topic Submit
    public function delete($topicid, $sure = 0)
    {
        (new TopicsModel)->delTopic($topicid, $sure);
    }

    // Rename a Topic Submit
    public function rename($topicid)
    {
        (new TopicsModel)->rename($topicid, $this->current_user);
    }

    // Move a Topic Submit
    public function move($topicid)
    {
        (new TopicsModel)->move($topicid, $this->current_user);
    }

    // Lock a Topic Submit
    public function action($forumid, $topicid, $type)
    {
        (new TopicsModel)->action($forumid, $topicid, $type, $this->current_user);
    }

}