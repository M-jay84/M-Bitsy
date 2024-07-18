<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\LogsModel;

/*
* Shoutbox Table Data
*/
class ShoutboxModel
{
    // Delete Shout
    public static function remove($updateset)
    {
        $stmt = DB::delete('shoutbox', $updateset);
		return $stmt;
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
    public static function truncate($sure = 0)
    {
        if ($sure == 'sure') {
        
            DB::truncate('shoutbox');
            LogsModel::write("Shoutbox cleared by ".$_SESSION['username']."");
            $msg_shout = "[color=#ff0000]" . Txt("SHOUTBOX_CLEARED_MESSAGE") . "[/color]";
            ShoutboxModel::insert(['userid'=>0, 'date'=>get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            Redirect(Url('admincp'), "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        } else {
            Redirect(Url('admincp'), "<b>Are You Sure <a href='".URLROOT."/adminshoutbox/clear/sure'>Clear Shoutbox</a></b>");
        }
    }

    // Add Shout
    public static function add($USER, $staff = 0)
    {
        if ($USER->has('shoutboxpos') != 'yes' && $USER->has('id')) {
            if (!empty($_POST['message']) && $USER->has('id')) {
                $message = Input('message');
                $count = DB::fetchColumn('shoutbox', 'COUNT(*)', 
                ['message=','user=',"UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) <"], 
                [$message, $USER->has('username'), get_date_time(), 30]);
                if ($count == '0') {
                    ShoutboxModel::insert(['userid'=>$USER->has('id'), 'date'=>get_date_time(), 'user'=>$USER->has('username'), 'message'=>$message, 'staff'=>$staff]);
                }
            }
        } else {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
    }

    // Delete Shout
    public static function delete($USER, $id, $sure = 0)
    {
        if ($sure == 0) {
            Redirect(Url(), "Sanity check: Click <a href='".URLROOT."/shoutbox/delete/$id/1'>here</a> if you are sure.");
        }

        $row = DB::fetchColumn('shoutbox', 'user', ['msgid ='], [$id]);
        if ($USER->permission('edit_users') || $USER->has('username') == $row['user']) {
            LogsModel::write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $USER->has('username') . "</b>");
            ShoutboxModel::remove(['msgid'=>$id]);
        }
        Redirect(Url());
    }

    // Edit Shout
    public static function edit()
    {
        // Check User Input
        $user = Input('user', 'int');
        if ($_SESSION['class'] > _UPLOADER || $_SESSION['id'] == $user) {
            // Check User Input
            $id = Input('íd', 'int');
            $message = Input('text');
            if ($message) {
                DB::update('shoutbox', ['message'], ['msgid='], [$message, $id]);
                Redirect(Url(), Txt("UPDATE_COMPLETE"));
            }
        } else {
            Redirect(Url('logout'), Txt("NO_PERMISSION"));
        }
    }

}