<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\ForumsModel;
use App\Models\ForumCatsModel;
use App\Models\ReadModel;
use App\Models\PostsModel;
use App\Models\AttachmentsModel;
use App\Models\ShoutboxModel;

/*
*  Topics Table Data
*/
class TopicsModel
{	
    // Fetch Topic Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_topics', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
	
    // Fetch All Topics
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_topics', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Topic Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_topics', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Get Topics
    public static function getForumTopics($forumid)
    {
        // Pagination
        $count = DB::fetchColumn('forum_topics', 'count(id)', ['forumid='], [$forumid]);
        list($pager, $limit) = pagination(25, $count, URLROOT . "/topic/$forumid&");
        $topics = DB::fetchAll('forum_topics', '*', ['forumid='], [$forumid], "ORDER BY lastpost DESC $limit");

        foreach ($topics as $arr) {
            $arr1 = ForumsModel::fetchRow('*', ['id='], [$forumid]);

            if (!$arr1['name'] || $_SESSION['class'] < $arr1["minclassread"] && $arr1["guest_read"] == "no") {
                Redirect(Url('forum'), Txt("FORUMS_NOT_PERMIT"));
            }

            $arr2 = PostsModel::fetchColumn('count(id)', ['topicid='], [$arr['id']]);
            $object[$arr['id']]['id'] = $arr['id'];
            $object[$arr['id']]['userid'] = $arr['userid'];
            $object[$arr['id']]['subject'] = $arr['subject'];
            $object[$arr['id']]['locked'] = $arr['locked'] == "yes";
            $object[$arr['id']]['forumid'] = $arr['forumid'];
            $object[$arr['id']]['lastpost'] = $arr['lastpost']; 
            $object[$arr['id']]['moved'] = $arr['moved'] == "yes";
            $object[$arr['id']]['sticky'] = $arr['sticky'] == "yes";
            $object[$arr['id']]['views'] = $arr['views'];

            $object[$arr['id']]['replys'] = $arr2;

            if ($_SESSION) {
                $r = ReadModel::fetchRow('lastpostread', ['userid=', 'topicid='], [$_SESSION['id'], $arr['id']]);
                $a = $r;
            }
            if ($new = !$a || $arr['lastpost'] > $a[0]) {
                $img = "<i class='fa fa-file-text tticon-red' title='Read'></i>";
            }
            $topicpic = $new ? "<i class='fa fa-file-text tticon-red' title='Read'></i>" : "<i class='fa fa-file-text' title='Read'></i>";
    
            $object[$arr['id']]['read'] = $topicpic;
            
            $arr3 = PostsModel::fetchRow('*', ['topicid='], [$arr['id']], 'ORDER BY id DESC LIMIT 1');
            $object[$arr['id']]['lastuserpost'] = "by&nbsp;".coloredname($arr3['userid'])."<br />".utc_to_tz($arr3['added'])."</span>";
            
            $object[$arr['id']]['added'] = $arr1['added'];
            $object[$arr['id']]['name'] = $arr1['name'];
            $object[$arr['id']]['minclassread'] = $arr1['minclassread'];
            $object[$arr['id']]['minclasswrite'] = $arr1['minclasswrite'];
            $object[$arr['id']]['guest_read'] = $arr1['guest_read'];
        }
        /*
        if (count($object) == 0) {
            Redirect(Url(), Txt("FORUM_AVAILABLE"));
        }
        */
        $fname = ForumCatsModel::fetchRow('name', ['id='], [$forumid]);
        $tname = ForumsModel::fetchRow('name', ['id='], [$forumid]);

        $array = [
            'topics' => $object,
            'forum_name' => $fname['name'],
            'topic_name' => $tname['name'],
            'pager' => $pager
        ];

        // Init Data
        return $array;
    }

    // Add New Topic
    public static function insertTopic($USER, $forumid)
    {

        $subject = Input('subject');
        $body = htmlspecialchars_decode($_POST["body"]);
        
        if (!$body || !$subject) {
            Redirect(Url('forum'), Txt("MISSING_FORM_DATA"));
        }

        // Make sure sure user has write access in forum
        $arr = ForumsModel::getForumAccessLevels($forumid) or Redirect(Url('forum'), "Bad forum ID");
        if ($USER->has('class') < $arr["write"]) {
            Redirect(Url('forum'), Txt("FORUMS_NOT_PERMIT"));
        }

        // Create Topic / Reply
        DB::insert('forum_topics', ['userid'=>$USER->has('id'), 'forumid'=>$forumid, 'subject'=>$subject]);
        $topicid = DB::lastInsertId() or Redirect(Url('forum'), "Topics id n/a");

        // Insert the new post
        PostsModel::insert(['topicid'=>$topicid, 'userid'=>$USER->has('id'), 'added'=>get_date_time(), 'body'=>$body]);
        $postid = DB::lastInsertId();

        // Update topic last post
        TopicsModel::update(['lastpost'], ['id='], [$postid, $topicid]);

        // attachments
        AttachmentsModel::set_attachment($topicid, $postid);

        // Insert Shout
        $msg_shout = "New Forum Topic: [url=" . URLROOT . "/post/" . $topicid . "]" . $subject . "[/url] posted by [color=".$USER->has('Color')."]".$USER->has('username')."[/color].";
        ShoutboxModel::insert(['userid'=>0, 'date'=>get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
        Redirect(Url("post/$topicid/last"));
    }

    // Delete Topic
    public static function delTopic($topicid, $sure = 0)
    {
       /*- 
        if (!Id($topicid) || !$USER->permission('delete_forum')) {
            Redirect(Url('forum'), Txt("NO_PERMISSION"));
        }
       */
        if ($sure == "0") {
            Redirect(Url('forum'), "Sanity check: You are about to delete a topic. Click <a href='" . URLROOT . "/topic/delete/$topicid/1'>here</a> if you are sure.");
        }

        DB::delete('forum_topics', ['id'=>$topicid]);
        PostsModel::delete(['topicid'=>$topicid]);
        ReadModel::delete(['topicid'=>$topicid]);
        AttachmentsModel::deleteAll($topicid);
        Redirect(Url('forum'), Txt("DEL_COMPLETE"));
    }

    // Delete Post
    public static function delete($id)
    {
        DB::delete('forum_topics', ['id'=>$id]);
    }

    // Update Topic
    public static function update($cols, $where, $values)
    {
        DB::update('forum_topics', $cols, $where, $values);
    }

    // Rename a Topic Submit
    public static function rename($topicid, $USER)
    {
        if (!$USER->permission('delete_forum') || !$USER->permission('edit_forum')) {
            Redirect(Url('forum'), Txt("NO_PERMISSION"));
        }

        // Check User Input
        $subject = Input('subject');

        if ($subject == '') {
            Redirect(Url('forum'), Txt("MISSING_FORM_DATA"));
        }

        // Update
        TopicsModel::update(['subject'], ['id='], [$subject,$topicid]);
        Redirect(Url("post/$topicid"), Txt("NAME_UPDATED"));
    }

    // Move a Topic Submit
    public static function move($topicid, $USER)
    {
        // Check User Input
        $forumid = Input('forumid', 'id');

        // Check User
        if (!Id($forumid) || !$USER->permission('delete_forum') || !$USER->permission('edit_forum')) {
            Redirect(Url('forum'), Txt("NO_PERMISSION"));
        }

        // Check Permisson To View
        $arr = ForumsModel::fetchRow('minclasswrite', ['id ='], [$forumid]);
        if ($arr->rowCount() != 1) {
            Redirect(Url('forum'), Txt("NOTHING_FOUND"));
        }
        if ($USER->has('class') < $arr['minclasswrite']) {
            Redirect(Url('forum'), Txt("NO_PERMISSION"));
        }

        // Move Topic
        $arr = TopicsModel::fetchRow('subject,forumid', ['id ='], $topicid);
        if ($arr->rowCount() != 1) {
            Redirect(Url('forum'), Txt("NOTHING_FOUND"));
        }
        if ($arr["forumid"] != $forumid) {
            TopicsModel::update(['forumid', 'moved'], ['id='], [$forumid,'yes',$topicid]);
        }

        Redirect(Url("topic/$forumid"));
    }

    // Lock/Sticky Topic Submit
    public static function action($forumid, $topicid, $type, $USER)
    {
        if ($type == 'sticky') {
            $action = ['sticky'];
            $values = ['yes',$topicid];
        } elseif ($type == 'unsticky') {
            $action = ['sticky'];
            $values = ['no',$topicid];
        } elseif ($type == 'lock') {
            $action = ['locked'];
            $values = ['yes',$topicid];
        } elseif ($type == 'unlock') {
            $action = ['locked'];
            $values = ['no',$topicid];
        }

        // Check User
        if (!Id($topicid) || !$USER->permission('delete_forum') || !$USER->permission('edit_forum')) {
            Redirect(Url('forum'), Txt("NO_PERMISSION"));
        }

        // Update
        TopicsModel::update($action, ['id='], $values);
        Redirect(Url("topic/$forumid"));
    }

}