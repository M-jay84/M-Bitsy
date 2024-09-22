<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsLogic;

/*
* Shoutbox Table Data
*/
class ShoutboxLogic
{
    // Delete Shout
    public static function remove($updateset)
    {
        $stmt = DB::delete('shoutbox', $updateset);
        showMessage(URLROOT, LANG["ACTION_COMPLETE"]);
	}
		
    // Insert Shout
    public static function insert($updateset)
    {
        $stmt = DB::insert('shoutbox', $updateset);
        return $stmt;
	}

    // Get Shouts
    public static function shouts($limit, $public = 0)
    {
        $shouts = DB::fetchAll('shoutbox', '*', ['staff='], [$public], "ORDER BY msgid DESC LIMIT $limit");
        return $shouts;
    }

    // Truncate All Shouts
    public static function truncate($current_user, $sure = 0)
    {
        if ($sure == 'sure') {
        
            DB::truncate('shoutbox');
            LogsLogic::write("Shoutbox cleared by ".$current_user->username."");
            $msg_shout = "[color=#ff0000]" . LANG["SHOUTBOX_CLEARED_MESSAGE"] . "[/color]";
            ShoutboxLogic::insert(['userid'=>0, 'date'=>CONFIG['TT_DATE'], 'user'=>'System', 'message'=>$msg_shout]);
            showMessage('admincp', "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        } else {
            showMessage('admincp', "<b>Are You Sure <a href='".URLROOT."/admintasks/clearshoutbox/sure'>delete all shouts</a></b>");
        }
    }

    // Add Shout
    public static function add($current_user, $staff = 0)
    {
        if ($current_user->shoutboxpos != 'yes' && $current_user->id) {
            if (!empty($_POST['message']) && $current_user->id) {
                $message = Validate::Input('message');
                $count = DB::fetchColumn('shoutbox', 'COUNT(*)', 
                ['message=','user=',"UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) <"], 
                [$message, $current_user->username, CONFIG['TT_DATE'], 30]);
                if ($count == '0') {
                    ShoutboxLogic::insert(['userid'=>$current_user->id, 'date'=>CONFIG['TT_DATE'], 'user'=>$current_user->username, 'message'=>$message, 'staff'=>$staff]);
                }
            }
        } else {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Delete Shout
    public static function delete($id, $current_user, $sure = 0)
    {
        if ($sure == 0) {
            showMessage(URLROOT, "Sanity check: Click <a href='".URLROOT."/shoutbox/delete/$id/1'>here</a> if you are sure.");
        }

        $row = DB::fetchColumn('shoutbox', 'user', ['msgid ='], [$id]);
        if ($current_user->edit_users == 'yes' || $current_user->username == $row['user']) {
            LogsLogic::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $current_user->username . "</b>");
            ShoutboxLogic::remove(['msgid'=>$id]);
        }
        Url(URLROOT);
    }

    // Edit Shout
    public static function edit($current_user)
    {
        // Check User Input
        $user = Validate::Input('user', 'int');
        if ($current_user->isStaff || $current_user->id == $user) {
            // Check User Input
            $msgid = Validate::Input('id', 'int');
            $message = Validate::Input('text');
            DB::update('shoutbox', ['message'], ['msgid='], [$message, $msgid]);
            showMessage(URLROOT, LANG["UPDATE_COMPLETE"]);
        } else {
            showMessage('logout', LANG["NO_PERMISSION"]);
        }
    }

}