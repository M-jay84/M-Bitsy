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
    public static function remove($id)
    {
        $shoutbox_query = "DELETE FROM shoutbox WHERE id = ?";
        $stmt = DB::run($shoutbox_query, [$id]);
        showMessage(URLROOT, LANG["ACTION_COMPLETE"]);
	}

    // Get Shouts
    public static function shouts($limit, $public = 0)
    {
        $shoutbox_query = "SELECT * FROM `shoutbox` WHERE staff=? ORDER BY msgid DESC LIMIT $limit";
        $shouts = DB::run($shoutbox_query, [$public]);
        return $shouts;
    }

    // Truncate All Shouts
    public static function truncate($current_user, $sure = 0)
    {
        if ($sure == 'sure') {
        
            $shoutbox_query = "TRUNCATE TABLE shoutbox";
            DB::run($shoutbox_query);
            LogsLogic::write("Shoutbox cleared by ".$current_user->username."");
            $msg_shout = "[color=#ff0000]" . LANG["SHOUTBOX_CLEARED_MESSAGE"] . "[/color]";
            $shoutbox_query = "INSERT INTO shoutbox (userid,date,user,message) VALUES (?,?,?,?)";
            DB::run($shoutbox_query, [0, CONFIG['TT_DATE'], 'System', $msg_shout]);
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
                $shoutbox_query = "SELECT count(*) FROM `shoutbox` WHERE message=? AND user=? AND UNIX_TIMESTAMP(?)-UNIX_TIMESTAMP(date) <?";
                $count =  DB::run($shoutbox_query, [$message, $current_user->username, CONFIG['TT_DATE'], 30])->fetchColumn();
                if ($count == '0') {

                    DB::run($shoutbox_query, [$current_user->id, CONFIG['TT_DATE'], $current_user->username, $message, $staff]);
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

        $shoutbox_query = "SELECT user FROM `shoutbox` WHERE msgid=?";
        $row =  DB::run($shoutbox_query, [$id])->fetchColumn();
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
            $shoutbox_query = "UPDATE shoutbox SET message = ? WHERE msgid=?";
            DB::run($shoutbox_query, [$message, $msgid]);
            showMessage(URLROOT, LANG["UPDATE_COMPLETE"]);
        } else {
            showMessage('logout', LANG["NO_PERMISSION"]);
        }
    }

}