<?php

namespace App\Models;

use App\Libs\DB;

/*
* Notifications Table Data
*/
class NotificationsModel
{	
    // Get Notifications
    public function getAll($current_userid) {
        $notif = DB::fetchAll('notifications', 'id, user_id AS uid, message, added, `read`, title', ['user_id='], [$current_userid]);
        
        $data = [
            'count' => $notif->rowCount(),
            'result' => $notif
        ];
        return $data;
    }

    // Read Notification
    public function updateRead($id,$current_userid ) {
        DB::run("UPDATE notifications 
            SET `read` = 'yes' 
            WHERE id = ? AND user_id = ?",
            [$id, $current_userid]);
        return true;
    }

    // Get Notification
    public function getById($id, $current_userid) {
        $res = DB::fetchRow('notifications', 'id, user_id', ['user_id=', 'id='], [$current_userid, $id]);
        if (!$res) {
            showMessage(Url('notification'), LANG["MISSING_ID"]);
        }
        return $res;
    }

    // Delete Notification
    public function deleteById($id, $current_userid)
    {
        // Check User
        if (!$current_userid) {
            showMessage(Url(), LANG["NO_USER_VIEW"]);
        }
        // Delete Data
        if (!empty($_GET)) {
            $delete = DB::delete('notifications', ['id' => $id, 'user_id' => $current_userid]);
            if ($delete) {
                showMessage(Url('notification'), LANG['DEL_COMPLETE']);
            } else {
                showMessage(Url('notification'), LANG['DEL_ERROR']);
            }
        }
    }
    
}