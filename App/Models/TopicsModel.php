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
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_topics', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
	
    // Fetch All Topics
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_topics', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Topic Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_topics', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Get Topics
    public function getForumTopics($forumid, $current_user)
    {
        // Pagination
        $count = DB::fetchColumn('forum_topics', 'count(id)', ['forumid='], [$forumid]);
        list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'] . "/topic/$forumid&");
        $topics = DB::fetchAll('forum_topics', '*', ['forumid='], [$forumid], "ORDER BY lastpost DESC $limit");

        foreach ($topics as $arr) {
            $arr1 = (new ForumsModel)->fetchRow('*', ['id='], [$forumid]);

            if (!$arr1['name'] || $current_user->class < $arr1["minclassread"] && $arr1["guest_read"] == "no") {
                showMessage(Url('forum'), LANG["FORUMS_NOT_PERMIT"]);
            }

            $arr2 = (new PostsModel)->fetchColumn('count(id)', ['topicid='], [$arr['id']]);
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
                $r = (new ReadModel)->fetchRow('lastpostread', ['userid=', 'topicid='], [$current_user->id, $arr['id']]);
                $a = $r;
            }
            if ($new = !$a || $arr['lastpost'] > $a[0]) {
                $img = "<i class='fa fa-file-text tticon-red' title='Read'></i>";
            }
            $topicpic = $new ? "<i class='fa fa-file-text tticon-red' title='Read'></i>" : "<i class='fa fa-file-text' title='Read'></i>";
    
            $object[$arr['id']]['read'] = $topicpic;
            
            $arr3 = (new PostsModel)->fetchRow('*', ['topicid='], [$arr['id']], 'ORDER BY id DESC LIMIT 1');
            $object[$arr['id']]['lastuserpost'] = "by&nbsp;".coloredname($arr3['userid'])."<br />".utc_to_tz($arr3['added'])."</span>";
            
            $object[$arr['id']]['added'] = $arr1['added'];
            $object[$arr['id']]['name'] = $arr1['name'];
            $object[$arr['id']]['minclassread'] = $arr1['minclassread'];
            $object[$arr['id']]['minclasswrite'] = $arr1['minclasswrite'];
            $object[$arr['id']]['guest_read'] = $arr1['guest_read'];
        }
        /*
        if (count($object) == 0) {
            showMessage(Url(), LANG["FORUM_AVAILABLE"));
        }
        */
        $fname = ForumCatsModel::fetchRow('name', ['id='], [$forumid]);
        $tname = (new ForumsModel)->fetchRow('name', ['id='], [$forumid]);

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
    public function insertTopic($forumid, $data)
    {

        $subject = Input('subject');
        $body = htmlspecialchars_decode($_POST["body"]);

        if (!$body || !$subject) {
            showMessage(Url('forum'), LANG["MISSING_FORM_DATA"]);
        }

        // Make sure sure user has write access in forum
        $arr = (new ForumsModel)->getForumAccessLevels($forumid) or showMessage(Url('forum'), "Bad forum ID");
        if ($data['current_user']->class < $arr["write"]) {
            showMessage(Url('forum'), LANG["FORUMS_NOT_PERMIT"]);
        }

        // Create Topic / Reply
        DB::insert('forum_topics', ['userid'=>$data['current_user']->id, 'forumid'=>$forumid, 'subject'=>$subject]);
        $topicid = DB::lastInsertId() or showMessage(Url('forum'), "Topics id n/a");

        // Insert the new post
        (new PostsModel)->insert(['topicid'=>$topicid, 'userid'=>$data['current_user']->id, 'added'=>get_date_time(), 'body'=>$body]);
        $postid = DB::lastInsertId();

        // Update topic last post
        DB::update('forum_topics', ['lastpost'], ['id='], [$postid, $topicid]);

        // attachments
        
        $data['current_user'] = $data['current_user'];
        $data['topicid'] = $topicid;
        $data['postid'] = $postid;
        (new AttachmentsModel)->set_attachment($data);
        // Insert Shout
        $msg_shout = "New Forum Topic: [url=" . CONFIG['URLROOT'] . "/post/" . $topicid . "]" . $subject . "[/url] posted by [color=".$data['current_user']->Color."]".$data['current_user']->username."[/color].";
        (new ShoutboxModel)->insert(['userid'=>0, 'date'=>get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
        
        
        Url(CONFIG['URLROOT']."/post/$topicid", true); // die; todo
    }

    // Delete Topic
    public function delTopic($topicid, $sure = 0)
    {
       /*- 
        if (!Id($topicid) || !$data['current_user']->delete_news'] == 'yes') {
            showMessage(Url('forum'), LANG["NO_PERMISSION"));
        }
       */
        if ($sure == "0") {
            showMessage(Url('forum'), "Sanity check: You are about to delete a topic. Click <a href='" . CONFIG['URLROOT'] . "/topic/delete/$topicid/1'>here</a> if you are sure.");
        }

        DB::delete('forum_topics', ['id'=>$topicid]);
        (new PostsModel)->delete(['topicid'=>$topicid]);
        (new ReadModel)->delete(['topicid'=>$topicid]);
        (new AttachmentsModel)->deleteAll($topicid);
        showMessage(Url('forum'), LANG["DEL_COMPLETE"]);
    }

    // Delete Post
    public function delete($id)
    {
        DB::delete('forum_topics', ['id'=>$id]);
    }

    // Update Topic
    public function update($cols, $where, $values)
    {
        DB::update('forum_topics', $cols, $where, $values);
    }

    // Rename a Topic Submit
    public function rename($topicid, $current_user)
    {
        if (!$current_user->delete_news == 'yes' || !$current_user->edit_forum == 'yes') {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Check User Input
        $subject = Input('subject');

        if ($subject == '') {
            showMessage(Url('forum'), LANG["MISSING_FORM_DATA"]);
        }

        // Update
        (new TopicsModel)->update(['subject'], ['id='], [$subject,$topicid]);
        showMessage(Url("post/$topicid"), LANG["NAME_UPDATED"]);
    }

    // Move a Topic Submit
    public function move($topicid, $current_user)
    {
        // Check User Input
        $forumid = Input('forumid', 'id');

        // Check User
        if (!Id($forumid) || !$current_user->delete_news == 'yes' || !$current_user->edit_forum == 'yes') {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Check Permisson To View
        $arr = (new ForumsModel)->fetchRow('minclasswrite', ['id ='], [$forumid]);
        if ($arr->rowCount() != 1) {
            showMessage(Url('forum'), LANG["NOTHING_FOUND"]);
        }
        if ($current_user->class < $arr['minclasswrite']) {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Move Topic
        $arr = (new TopicsModel)->fetchRow('subject,forumid', ['id ='], $topicid);
        if ($arr->rowCount() != 1) {
            showMessage(Url('forum'), LANG["NOTHING_FOUND"]);
        }
        if ($arr["forumid"] != $forumid) {
            (new TopicsModel)->update(['forumid', 'moved'], ['id='], [$forumid,'yes',$topicid]);
        }

        Url(Url("topic/$forumid", true));
    }

    // Lock/Sticky Topic Submit
    public function action($forumid, $topicid, $type, $current_user)
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
        if (!Id($topicid) || !$current_user->delete_news == 'yes' || !$current_user->edit_forum == 'yes') {
            showMessage(Url('forum'), LANG["NO_PERMISSION"]);
        }

        // Update
        (new TopicsModel)->update($action, ['id='], $values);
        Url(Url("topic/$forumid"), true);
    }

}