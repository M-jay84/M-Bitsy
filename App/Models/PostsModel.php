<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\TopicsModel;
use App\Models\ForumsModel;
use App\Models\ForumCatsModel;
use App\Models\ReadModel;
use App\Models\AttachmentsModel;

/*
* Post Table Data
*/
class PostsModel
{
    // Delete Post
    public static function delete($data)
    {
        $stmt =  DB::delete('forum_posts', $data);
        return $stmt;
    }

    // Update Post
    public static function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('forum_posts', $cols, $where, $vals, $other);
        return $stmt;
	}

    // Fetch Torrent Details
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_posts', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Torrent Details
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_posts', $columns, $where, $values, $other);
		return $stmt;
	}

    // Fetch Torrent Details
    public static function insert($updateset)
    {
        $stmt = DB::insert('forum_posts', $updateset);
        return $stmt;
	}

    // Fetch Torrent Details
    public static function postcount($id)
    {
        $stmt = DB::run("SELECT count(id) as count FROM forum_posts 
		WHERE topicid IN (SELECT id FROM forum_topics 
		WHERE forumid=?)", [$id])->fetchColumn();
		return $stmt;
	}
	
    // Returns the ID of the last post of a forum
    public static function updateTopicLastPost($topicid)
    {
        $res = DB::fetchRow('forum_posts', 'id', ['topicid='], [$topicid], 'ORDER BY id DESC LIMIT 1');
        $arr = $res or showMessage(('forum'), 'No post found');
        $postid = $arr[0];
        TopicsModel::update(['lastpost'], ['id='], [$topicid, $postid]);
    }

    // Returns the forum ID of a topic, or false on error
    public static function getTopicForum($topicid)
    {
        $res = TopicsModel::fetchRow('forumid', ['id='], [$topicid]);
        if (!$res) {
            return false;
        }
        return $res['forumid'];
    }

    // Get Post
    public static function getTopicPages($topicid, $current_user)
    {
        // Get Post Data
        $count = DB::fetchColumn('forum_posts', 'COUNT(id)', ['topicid='], [$topicid]);
        list($pager, $limit) = pagination(15, $count, URLROOT . "/post/$topicid?");
        $query =   "SELECT forum_posts.id,
                        forum_posts.topicid,
                        forum_posts.userid,
                        forum_posts.added,
                        forum_posts.body,
                        forum_posts.editedby,
                        forum_posts.editedat,
                        forum_posts.attachments
                    FROM forum_posts
                    LEFT JOIN forum_topics 
                    ON forum_topics.id = forum_posts.topicid
                    LEFT JOIN likes 
                    ON forum_posts.id = likes.liked
                    WHERE forum_posts.topicid = $topicid
                    GROUP BY forum_posts.id 
                    ORDER BY forum_posts.id $limit";
					/*
                        likes.user,
                        likes.liked,
                        likes.type
					*/
        $stmt = DB::run($query)->fetchAll();

        // Get Topic Data
        $arr = TopicsModel::fetchRow('*', ['id='], [$topicid]);
        $levels = ForumsModel::getForumAccessLevels($arr['forumid']) or showMessage(('forum'), 'issue with access');
        $maypost = $current_user->class >= $levels["write"] ? true : false;

        // Update Topic Views
        $pvkey_var = "f_".$topicid;
        $views = $_SESSION[$pvkey_var] == 1 ? $arr["views"] + 0 : $arr["views"] + 1;
        $_SESSION[$pvkey_var] = 1;
        TopicsModel::update(['views'], ['id='], [$views,$topicid]);

        // Check if user has access to this forum
        $arr2 = ForumsModel::fetchRow('*', ['id='], [$arr['forumid']]);
        if (!$arr2 || $current_user->class < $arr2["minclassread"] && $arr2["guest_read"] == "no") {
            showMessage(('forum'), LANG["NO_PERMISSION"]);
        }

        // Update Last Read
        if ($current_user->id) {
            $lpr = ReadModel::fetchColumn('lastpostread', ['userid=','topicid='], [$current_user->id,$topicid]);
            if (!$lpr) {
               DB::insert(' forum_readposts', ['userid'=>$current_user->id, 'topicid'=>$topicid]);
            }
        }
        $fname = ForumCatsModel::fetchRow('name', ['id='], [$arr['forumid']]);

        $array = [
            'locked' => $arr["locked"] == 'yes',
            'subject' => stripslashes($arr["subject"]),
            'sticky' => $arr["sticky"] == "yes",
            'forumid' => $arr['forumid'],
            'minclasswrite' => $levels['write'],
            'guest_read' => $levels['read'],
            'maypost' => $maypost,
            'name' => stripslashes($arr2["name"]),
            'arr2' => $arr2,
            'stmt' => $stmt,
            'pager' => $pager,
            'lpr' => $lpr,
            'forum_name' => $fname['name']
        ];

        return $array;
    }

    // Delete Post
    public static function deletePost($postid, $data, $sure = 0)
    {        // Check User
        if (!$data['current_user']->delete_news == 'yes' || !intval($postid)) {
            showMessage(('forum'), LANG["NO_PERMISSION"]);
        }
        
	    // Double Check
        if ($sure) {
		    showMessage(('forum'), "Sanity check: You are about to delete a post. Click <a href='" . URLROOT . "/post/delete/$postid/1'>here</a> if you are sure.");
        }

        $topicid = DB::fetchColumn('forum_posts', 'topicid', ['id='], [$postid]);

        // We can not delete the post if it is the only one of the topic
        $arr = DB::fetchColumn('forum_posts', 'COUNT(id)', ['topicid='], [$topicid]);
        if ($arr == 1) {
            $msg = sprintf(LANG["FORUMS_DEL_POST_ONLY_POST"], $topicid);
            showMessage(('forum'), $msg);
        }

        // Delete post
        DB::delete('forum_posts', ['id'=>$postid]);
        AttachmentsModel::deleteAll($topicid, true);
        PostsModel::updateTopicLastPost($topicid);
        showMessage(("post/$topicid"), LANG["DEL_COMPLETE"]);
    }

    public static function addPost($topicid, $current_user) // todo
    {
        $body = htmlspecialchars_decode($_POST["body"]);

        if (!$body) {
            showMessage(('forum'), LANG["MISSING_FORM_DATA"]);
        }

        $forumid = PostsModel::getTopicForum($topicid) or showMessage(('forum'), "Bad topic ID");

        // Make sure sure user has write access in forum
        $arr = ForumsModel::getForumAccessLevels($forumid) or showMessage(('forum'), "Bad forum ID");
        if ($current_user->class < $arr["write"]) {
            showMessage(('forum'), LANG["FORUMS_NOT_PERMIT"]);
        }

        //Make sure topic exists and is unlocked
        $arr = TopicsModel::fetchRow('*', ['id ='], [$topicid]);
        if ($arr["locked"] == 'yes') {
            showMessage(('forum'), LANG["LOCKED"]);
        }
        //Get forum ID
        $forumid = $arr["forumid"];

        // Insert the new post
        $postid = PostsModel::insert(['topicid'=>$topicid, 'userid'=>$current_user->id, 'added'=>CONFIG['TT_DATE'], 'body'=>$body]);

        // attachments
        $data['topicid'] = $topicid;
        $data['postid'] = $postid;
        AttachmentsModel::set_attachment($data);

        // Update topic last post
        PostsModel::updateTopicLastPost($topicid);

        showMessage(("post/$topicid/last/#post$postid"), true);
    }

    // Post Edit Form Submit
    public static function edit($data)
    {
        // Check User Input
        $postid = Validate::Input('postid', 'int');

        // Check User Input
        $body = htmlspecialchars_decode($_POST['body']);

        // Check Correct Input
        if ($body == "") {
            showMessage(('forum'), LANG["MISSING_FORM_DATA"]);
        }

        // Get Topic Data
        $arr = PostsModel::fetchRow('*', ['id ='], [$postid]);
        if (!$arr) {
            showMessage(('forum'), LANG["MISSING_ID"]. $postid);
        }
            
        // Check User
        if ($data['current_user']->id != $arr["userid"] && !$data['current_user']->delete_news == 'yes' && !$data['current_user']->edit_forum == 'yes') {
            showMessage(('forum'), LANG["NO_PERMISSION"]);
        }

        // Edit Post
        DB::update('forum_posts', ['body', 'editedat', 'editedby'], ['id='], [$body,CONFIG['TT_DATE'],$data['current_user']->id,$postid]);
        $data['current_user'] = $data['current_user'];
        $data['topicid'] = $arr['topicid'];
        $data['postid'] = $postid;
        AttachmentsModel::set_attachment($data);
        showMessage(("post/$arr[topicid]/$_POST[page]/#post$postid"), "Post was edited successfully.");
    }
    
}