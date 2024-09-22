<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TopicsLogic;

/*
* Topic Controller
*/
class Topic extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    
        if (!CONFIG['FORUMS']) {
            showMessage(URLROOT, LANG["FORUM_AVAILABLE"]);
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$this->current_user->loggedind) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if ($this->current_user->view_forum != "yes" && !$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["FORUM_BANNED"]);
        }
    }

    // View Forum Topic Page
    public function index($forumid)
    {
        // Get Topics Data
        $topics = TopicsLogic::getForumTopics($forumid, $this->current_user);

        // Data For View
        $data = [
            'TITLE' => LANG["FORUM"],
            'TOPICS' => $topics,
            'FORUM_ID' => $forumid,
            'TOPIC_NAME' => $topics['topic_name'],
            'FORUM_NAME' => $topics['forum_name'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('user-topic', $data, 'user');
    }

    // Post Topic
    public function submit($forumid)
    {
        TopicsLogic::insertTopic($forumid, $this->current_user);
    }

    // Delete a Topic Submit
    public function delete($topicid, $sure = 0)
    {
        TopicsLogic::delTopic($topicid, $sure);
    }

    // Rename a Topic Submit
    public function rename($topicid)
    {
        TopicsLogic::rename($topicid, $this->current_user);
    }

    // Move a Topic Submit
    public function move($topicid)
    {
        TopicsLogic::move($topicid, $this->current_user);
    }

    // Lock a Topic Submit
    public function action($forumid, $topicid, $type)
    {
        TopicsLogic::action($forumid, $topicid, $type, $this->current_user);
    }

}