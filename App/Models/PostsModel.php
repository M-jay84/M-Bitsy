<?php

namespace App\Models;

use App\Libs\DB;
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
    public function delete($data)
    {
        $stmt =  DB::delete('forum_posts', $data);
        return $stmt;
    }

    // Update Post
    public function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('forum_posts', $cols, $where, $vals, $other);
        return $stmt;
	}

    // Fetch Torrent Details
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_posts', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Torrent Details
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_posts', $columns, $where, $values, $other);
		return $stmt;
	}

    // Fetch Torrent Details
    public function insert($updateset)
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
    public function updateTopicLastPost($topicid)
    {
        $res = DB::fetchRow('forum_posts', 'id', ['topicid='], [$topicid], 'ORDER BY id DESC LIMIT 1');
        $arr = $res or showMessage(Url('forum'), 'No post found');
        $postid = $arr[0];
        (new TopicsModel)->update(['lastpost'], ['id='], [$topicid, $postid]);
    }

    // Returns the forum ID of a topic, or false on error
    public function getTopicForum($topicid)
    {
        $res = (new TopicsModel)->fetchRow('forumid', ['id='], [$topicid]);
        if (!$res) {
            return false;
        }
        return $res['forumid'];
    }

    // Get Post
    public function getTopicPages($topicid, $current_user)
    {
        // Get Post Data
        $count = DB::fetchColumn('forum_posts', 'COUNT(id)', ['topicid='], [$topicid]);
        list($pager, $limit) = pagination(15, $count, CONFIG['URLROOT'] . "/post/$topicid?");
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
        $arr = (new TopicsModel)->fetchRow('*', ['id='], [$topicid]);
        $levels = (new ForumsModel)->getForumAccessLevels($arr['forumid']) or showMessage(Url('forum'), 'issue with access');
        $maypost = $current_user->class >= $levels["write"] ? true : false;

        // Update Topic Views
        $pvkey_var = "f_".$topicid;
        $views = $_SESSION[$pvkey_var] == 1 ? $arr["views"] + 0 : $arr["views"] + 1;
        $_SESSION[$pvkey_var] = 1;
        (new TopicsModel)->update(['views'], ['id='], [$views,$topicid]);

        // Check if user has access to this forum
        $arr2 = (new ForumsModel)->fetchRow('*', ['id='], [$arr['forumid']]);
        if (!$arr2 || $current_user->class < $arr2["minclassread"] && $arr2["guest_read"] == "no") {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Update Last Read
        if ($current_user->id) {
            $lpr = (new ReadModel)->fetchColumn('lastpostread', ['userid=','topicid='], [$current_user->id,$topicid]);
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
    public function deletePost($postid, $data, $sure = 0)
    {        // Check User
        if (!$data['current_user']->delete_news == 'yes' || !Id($postid)) {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }
        
	    // Double Check
        if ($sure) {
		    showMessage(Url('forum'), "Sanity check: You are about to delete a post. Click <a href='" . CONFIG['URLROOT'] . "/post/delete/$postid/1'>here</a> if you are sure.");
        }

        $topicid = DB::fetchColumn('forum_posts', 'topicid', ['id='], [$postid]);

        // We can not delete the post if it is the only one of the topic
        $arr = DB::fetchColumn('forum_posts', 'COUNT(id)', ['topicid='], [$topicid]);
        if ($arr == 1) {
            $msg = sprintf(LANG["FORUMS_DEL_POST_ONLY_POST"], $topicid);
            showMessage(Url('forum'), $msg);
        }

        // Delete post
        DB::delete('forum_posts', ['id'=>$postid]);
        (new AttachmentsModel)->deleteAll($topicid, true);
        (new PostsModel)->updateTopicLastPost($topicid);
        showMessage(Url("post/$topicid"), LANG["DEL_COMPLETE"]);
    }

    public function addPost($topicid, $current_user) // todo
    {
        $body = htmlspecialchars_decode($_POST["body"]);

        if (!$body) {
            showMessage(Url('forum'), LANG["MISSING_FORM_DATA"]);
        }

        $forumid = (new PostsModel)->getTopicForum($topicid) or showMessage(Url('forum'), "Bad topic ID");

        // Make sure sure user has write access in forum
        $arr = (new ForumsModel)->getForumAccessLevels($forumid) or showMessage(Url('forum'), "Bad forum ID");
        if ($current_user->class < $arr["write"]) {
            showMessage(Url('forum'), LANG["FORUMS_NOT_PERMIT"]);
        }

        //Make sure topic exists and is unlocked
        $arr = (new TopicsModel)->fetchRow('*', ['id ='], [$topicid]);
        if ($arr["locked"] == 'yes') {
            showMessage(Url('forum'), LANG["LOCKED"]);
        }
        //Get forum ID
        $forumid = $arr["forumid"];

        // Insert the new post
        $postid = (new PostsModel)->insert(['topicid'=>$topicid, 'userid'=>$current_user->id, 'added'=>get_date_time(), 'body'=>$body]);

        // attachments
        $data['topicid'] = $topicid;
        $data['postid'] = $postid;
        (new AttachmentsModel)->set_attachment($data);

        // Update topic last post
        (new PostsModel)->updateTopicLastPost($topicid);

        showMessage(Url("post/$topicid/last/#post$postid"), true);
    }

    // Post Edit Form Submit
    public function edit($data)
    {
        // Check User Input
        $postid = Input('postid', 'int');

        // Check User Input
        $body = htmlspecialchars_decode($_POST['body']);

        // Check Correct Input
        if ($body == "") {
            showMessage(Url('forum'), LANG["MISSING_FORM_DATA"]);
        }

        // Get Topic Data
        $arr = (new PostsModel)->fetchRow('*', ['id ='], [$postid]);
        if (!$arr) {
            showMessage(Url('forum'), LANG["MISSING_ID"]. $postid);
        }
            
        // Check User
        if ($data['current_user']->id != $arr["userid"] && !$data['current_user']->delete_news == 'yes' && !$data['current_user']->edit_forum == 'yes') {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Edit Post
        DB::update('forum_posts', ['body', 'editedat', 'editedby'], ['id='], [$body,get_date_time(),$data['current_user']->id,$postid]);
        $data['current_user'] = $data['current_user'];
        $data['topicid'] = $arr['topicid'];
        $data['postid'] = $postid;
        (new AttachmentsModel)->set_attachment($data);
        showMessage(Url("post/$arr[topicid]/$_POST[page]/#post$postid"), "Post was edited successfully.");
    }
    
}