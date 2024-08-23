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
    public function remove($updateset)
    {
        $stmt = DB::delete('shoutbox', $updateset);
		return $stmt;
	}
		
    // Insert Shout
    public function insert($updateset)
    {
        $stmt = DB::insert('shoutbox', $updateset);
        return $stmt;
	}

    // Get Shouts
    public function shouts($limit, $public = 0)
    {
        $shouts = DB::fetchAll('shoutbox', '*', ['staff='], [$public], "ORDER BY msgid DESC LIMIT $limit");
        return $shouts;
    }

    // Truncate All Shouts
    public function truncate($current_user, $sure = 0)
    {
        if ($sure == 'sure') {
        
            DB::truncate('shoutbox');
            (new LogsModel)->write("Shoutbox cleared by ".$current_user->username."");
            $msg_shout = "[color=#ff0000]" . LANG["SHOUTBOX_CLEARED_MESSAGE"] . "[/color]";
            (new ShoutboxModel)->insert(['userid'=>0, 'date'=>get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);
            showMessage(Url('admincp'), "<b><font color='#ff0000'>Shoutbox Cleared....</font></b>");
        } else {
            showMessage(Url('admincp'), "<b>Are You Sure <a href='".CONFIG['URLROOT']."/adminshoutbox/clear/sure'>Clear Shoutbox</a></b>");
        }
    }

    // Add Shout
    public function add($current_user, $staff = 0)
    {
        if ($current_user->shoutboxpos != 'yes' && $current_user->id) {
            if (!empty($_POST['message']) && $current_user->id) {
                $message = Input('message');
                $count = DB::fetchColumn('shoutbox', 'COUNT(*)', 
                ['message=','user=',"UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) <"], 
                [$message, $current_user->username, get_date_time(), 30]);
                if ($count == '0') {
                    (new ShoutboxModel)->insert(['userid'=>$current_user->id, 'date'=>get_date_time(), 'user'=>$current_user->username, 'message'=>$message, 'staff'=>$staff]);
                }
            }
        } else {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
    }

    // Delete Shout
    public function delete($id, $current_user, $sure = 0)
    {
        if ($sure == 0) {
            showMessage(Url(), "Sanity check: Click <a href='".CONFIG['URLROOT']."/shoutbox/delete/$id/1'>here</a> if you are sure.");
        }

        $row = DB::fetchColumn('shoutbox', 'user', ['msgid ='], [$id]);
        if ($current_user->edit_users == 'yes' || $current_user->username == $row['user']) {
            (new LogsModel)->write("<b><font color='orange'>Shout Deleted:</font> Deleted by   " . $current_user->username . "</b>");
            (new ShoutboxModel)->remove(['msgid'=>$id]);
        }
        Url(Url());
    }

    // Edit Shout
    public function edit($current_user)
    {
        // Check User Input
        $user = Input('user', 'int');
        if ($current_user > CONFIG['_UPLOADER'] || $current_user->id == $user) {
            // Check User Input
            $id = Input('íd', 'int');
            $message = Input('text');
            if ($message) {
                DB::update('shoutbox', ['message'], ['msgid='], [$message, $id]);
                showMessage(Url(), LANG["UPDATE_COMPLETE"]);
            }
        } else {
            showMessage(Url('logout'), LANG["NO_PERMISSION"]);
        }
    }

}