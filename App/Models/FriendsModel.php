<?php

namespace App\Models;

use App\Libs\DB;

/*
* Friends Table Data
*/
class FriendsModel
{
    // Count Friends
    public function countFriendAndEnemy($userid, $id)
    {
        $friend = DB::fetchColumn('friends', 'count(id)', ['userid=', 'friend=', 'friendid='], [$userid, 'friend', $id]);
        $block = DB::fetchColumn('friends', 'count(id)', ['userid=', 'friend=', 'friendid='], [$userid, 'enemy', $id]);

        $arr = [
            'friend' => $friend,
            'enemy' => $block
        ];
        return $arr;
    }
		
    // Fetch Friends Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('friends', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Get Friends
    public function join($user1, $data = [], $type = '')
    {
        if ($data['current_user']->view_users != "yes" && $data['current_user']->id != $user1) {
            showMessage(Url(), LANG["NO_USER_VIEW"]);
        }

        $stmt = DB::run("SELECT f.friendid as id, 
        u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
        FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id 
        WHERE userid=? AND friend=? ORDER BY name", [$user1, $type]);
        return $stmt;
    }

    // Add Friend/Block
    public function add($targetid, $data = [], $type = '')
    {
        if ($type == 'friend') {
            $r = DB::fetchColumn('friends', 'count(id)', ['userid=','userid=', 'friend='], [$data['current_user']->id,$targetid,'friend']);
            if ($r) {
                showMessage(Url("friend/".$data['current_user']->id.""), "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>$data['current_user']->id, 'friendid'=>$targetid, 'friend'=>'friend']);
            showMessage(Url("friend/".$data['current_user']->id.""), "User Friended");
        } elseif ($type == 'block') {
            $r = DB::fetchColumn('friends', 'count(id)', ['userid=','userid=', 'friend='], [$data['current_user']->id,$targetid,'enemy']);
            if ($r) {
                showMessage(Url("friend/".$data['current_user']->id.""), "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>$data['current_user']->id, 'friendid'=>$targetid, 'friend'=>'enemy']);
            showMessage(Url("friend/".$data['current_user']->id.""), "User Blocked");
        } else {
            showMessage(Url("friend/".$data['current_user']->id.""), "Unknown type $type");
        }
    }

    // Delete Friend/Block
    public function delete($targetid, $data = [], $type = '')
    {
        if ($type == 'friend') {
            $stmt = DB::delete('friends', ['userid' =>$data['current_user']->id, 'friendid' => $targetid, 'friend'=>'friend']);
            if ($stmt == 0) {
                showMessage(Url("profile/$targetid"), "No friend found with ID $targetid");
            }
            $frag = "friends";
        } elseif ($type == 'block') {
            $stmt = DB::delete('friends', ['userid' =>$data['current_user']->id, 'friendid' => $targetid, 'friend'=>'enemy']);
            if ($stmt == 0) {
                showMessage(Url("profile/$targetid"), "No block found with ID $targetid");
            }
            $frag = "blocked";
        } else {
            showMessage(Url("profile/$targetid"), "Unknown type $type");
        }
        showMessage(Url("friend/".$data['current_user']->id."#$frag"), "User Removed");
    }

}