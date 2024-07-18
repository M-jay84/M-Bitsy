<?php

namespace App\Models;

use App\Libs\DB;

/*
* Notifications Table Data
*/
class NotificationsModel
{
    // Fetch All Notifications
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('notifications', $columns, $where, $values, $other);           
		return $stmt;
	}
		
    // Add Notification
    public static function sendNotif($userid, $message, $title = '')
    {
       $notif = DB::run("INSERT INTO `notifications` (`user_id`, `title`, `message`, `added`, `read`) 
                     VALUES (?, ?, ?, ?, ?)",
                     [$userid, $title, $message, get_date_time(), 'no']
                );
       if ($notif) {
           return true;
       }
       return false;
    }

    // Get Notifications
    public static function getAll() {
        $notif = DB::fetchAll('notifications', 'id, user_id AS uid, message, added, `read`, title', ['user_id='], [$_SESSION['id']]);
        
        $data = [
            'count' => $notif->rowCount(),
            'result' => $notif
        ];
        return $data;
    }

    // Read Notification
    public static function updateRead($id) {
        DB::run("UPDATE notifications 
            SET `read` = 'yes' 
            WHERE id = ? AND user_id = ?",
            [$id, $_SESSION['id']]);
        return true;
    }

    // Get Notification
    public static function getById($id) {
        $res = DB::fetchRow('notifications', 'id, user_id', ['user_id=', 'id='], [$_SESSION['id'], $id]);
        if (!$res) {
            Redirect(Url('notification'), Txt("MISSING_ID"));
        }
        return $res;
    }

    // Delete Notification
    public static function deleteById($id)
    {
        // Check User
        if (!$_SESSION['id']) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }
        // Delete Data
        if (!empty($_GET)) {
            $delete = DB::delete('notifications', ['id' => $id, 'user_id' => $_SESSION['id']]);
            if ($delete) {
                Redirect(Url('notification'), Txt('DEL_COMPLETE'));
            } else {
                Redirect(Url('notification'), Txt('DEL_ERROR'));
            }
        }
    }
    
}