<?php

namespace App\Models;

use App\Libs\DB;

/*
* Friends Table Data
*/
class FriendsModel
{
    // Count Friends
    public static function countFriendAndEnemy($userid, $id)
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
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('friends', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Get Friends
    public static function join($USER, $user1, $type)
    {
        if (!$USER->permission('view_users', $user1)) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }

        $stmt = DB::run("SELECT f.friendid as id, 
        u.username AS name, u.class, u.avatar, u.title, u.enabled, u.last_access 
        FROM friends AS f LEFT JOIN users as u ON f.friendid = u.id 
        WHERE userid=? AND friend=? ORDER BY name", [$user1, $type]);
        return $stmt;
    }

    // Add Friend/Block
    public static function add($targetid, $type = '')
    {
        if ($type == 'friend') {
            $r = DB::fetchColumn('friends', 'count(id)', ['userid=','userid=', 'friend='], [$_SESSION['id'],$targetid,'friend']);
            if ($r) {
                Redirect(Url("friend/".$_SESSION['id'].""), "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>$_SESSION['id'], 'friendid'=>$targetid, 'friend'=>'friend']);
            Redirect(Url("friend/".$_SESSION['id'].""), "User Friended");
        } elseif ($type == 'block') {
            $r = DB::fetchColumn('friends', 'count(id)', ['userid=','userid=', 'friend='], [$_SESSION['id'],$targetid,'enemy']);
            if ($r) {
                Redirect(Url("friend/".$_SESSION['id'].""), "User ID $targetid is already in your friends list.");
            }
            DB::insert('friends', ['id'=>0, 'userid'=>$_SESSION['id'], 'friendid'=>$targetid, 'friend'=>'enemy']);
            Redirect(Url("friend/".$_SESSION['id'].""), "User Blocked");
        } else {
            Redirect(Url("friend/".$_SESSION['id'].""), "Unknown type $type");
        }
    }

    // Delete Friend/Block
    public static function delete($targetid, $type = '')
    {
        if ($type == 'friend') {
            $stmt = DB::delete('friends', ['userid' =>$_SESSION['id'], 'friendid' => $targetid, 'friend'=>'friend']);
            if ($stmt == 0) {
                Redirect(Url("profile/$targetid"), "No friend found with ID $targetid");
            }
            $frag = "friends";
        } elseif ($type == 'block') {
            $stmt = DB::delete('friends', ['userid' =>$_SESSION['id'], 'friendid' => $targetid, 'friend'=>'enemy']);
            if ($stmt == 0) {
                Redirect(Url("profile/$targetid"), "No block found with ID $targetid");
            }
            $frag = "blocked";
        } else {
            Redirect(Url("profile/$targetid"), "Unknown type $type");
        }
        Redirect(Url("friend/".$_SESSION['id']."#$frag"), "User Removed");
    }

}