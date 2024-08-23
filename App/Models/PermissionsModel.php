<?php

namespace App\Models;

use App\Libs\DB;

/*
* Permission Table Data
*/
class PermissionsModel
{
    // Fetch Permission Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('permissions', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch aLL PermissionS
    public function fetchALL($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('permissions', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Admin Delete Permissions
    public function delete($id)
    {
        // Protect Permissions
        if ($id == 1) {
            showMessage(Url('permission'), LANG["YOU_CANT_DEL_THIS_GRP"]);
        }

        DB::delete('`permissions`', ['permission_id'=>$id]);
        showMessage(Url('adminpermission'), LANG["DEL_COMPLETE"]);
    }

    // Admin Add/Update Permissions
    public function insertOrUpdate()
    {
        $group_id = Input('permission_id', 'int');
        
        if (!$group_id) {
            DB::insert('permissions', [
                'permission_name' => $_POST["permission_name"],
                'view_torrents' => $_POST["view_torrents"], 'edit_torrents' => $_POST["edit_torrents"], 'delete_torrents' => $_POST["delete_torrents"],
                'view_users' => $_POST["view_users"], 'edit_users' => $_POST["edit_users"], 'delete_users' => $_POST["delete_users"],
                'view_news' => $_POST["view_news"], 'edit_news' => $_POST["edit_news"], 'delete_news' => $_POST["delete_news"],
                'view_forum' => $_POST["view_forum"], 'edit_forum' => $_POST["edit_forum"], 'delete_forum' => $_POST["delete_forum"],
                'can_upload' => $_POST["can_upload"], 'can_download' => $_POST["can_download"]
            ]);
        } else {
            DB::update('permissions',
                ['permission_name','view_torrents','edit_torrents','delete_torrents','view_users','edit_users','delete_users',
                'view_news','edit_news','delete_news','view_forum','edit_forum','delete_forum','can_upload','can_download'], ['permission_id='],
                [$_POST["permission_name"], $_POST["view_torrents"], $_POST["edit_torrents"], $_POST["delete_torrents"],
                $_POST["view_users"], $_POST["edit_users"], $_POST["delete_users"], $_POST["view_news"], $_POST["edit_news"], $_POST["delete_news"],
                $_POST["view_forum"], $_POST["edit_forum"], $_POST["delete_forum"], $_POST["can_upload"], $_POST["can_download"], $group_id]);
        }

        showMessage(Url('adminpermission'), LANG["ACTION_COMPLETE"]);
    }

}