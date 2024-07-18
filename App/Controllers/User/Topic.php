<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TopicsModel;

/*
* Topic Controller
*/
class Topic 
{
    // View Forum Topic Page
    public function index($forumid)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        // Get Topics Data
        $topics = TopicsModel::getForumTopics($forumid);

        // Init Data
        $data = [
            'V_title' => Txt("FORUM"),
            'V_topics' => $topics,
            'V_forumid' => $forumid,
            'V_topic_name' => $topics['topic_name'],
            'V_forum_name' => $topics['forum_name'],
            'USER' => $USER
        ];

        // Load View
        template('topic', $data, 'user');
    }

    // Post Topic
    public function submit($forumid)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        TopicsModel::insertTopic($USER, $forumid);
    }

    // Delete a Topic Submit
    public function delete($topicid, $sure = 0)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        TopicsModel::delTopic($topicid, $sure);
    }

    // Rename a Topic Submit
    public function rename($topicid)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        TopicsModel::rename($topicid, $USER);
    }

    // Move a Topic Submit
    public function move($topicid)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        TopicsModel::move($topicid, $USER);
    }

    // Lock a Topic Submit
    public function action($forumid, $topicid, $type)
    {
        $USER = new User(0, 1);
        if (!CONFIG['FORUMS']) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        if (!CONFIG['FORUMS_GUESTREAD'] && !$USER->has('id')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (!$USER->permission('view_forum')) {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }
        TopicsModel::action($forumid, $topicid, $type, $USER);
    }

}