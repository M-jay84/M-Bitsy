<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;

/*
* Groups Table Data
*/
class GroupsLogic
{
    // Fetch Groups Level
    public static function fetchlevel($ids)
    {
        $stmt = DB::run("SELECT DISTINCT level FROM `groups` WHERE group_id IN ($ids)")->fetchAll();
		return $stmt;
	}
		
    // Fetch All Groups
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('groups', $columns, $where, $values, $other);
		return $stmt;
	}

    // Function That Returns The Group Name
    public static function GetUserClassName($i, $data = [])
    {
        $row = DB::fetchRow('`groups`', 'level', ['group_id ='], [$i]);
        if ($i == $data['current_user']->class) {
           return  $row['level'];
        }
        return $row['level'];
    }

    // Function To List Groups Of Members Of The Database
    public static function getGroups()
    {
        $stmt = DB::run("SELECT * FROM `groups` 
                         LEFT OUTER JOIN `permissions` 
                         ON `groups`.group_role_id=`permissions`.permission_id")->fetchAll();
        return $stmt;
    }

    // Admin Delete Group
    public static function delete($group_id)
    {
        // Protect Groups
        if (($group_id == "1") || ($group_id == CONFIG['_ADMINISTRATOR'])) {
            showMessage('admingroup', LANG["YOU_CANT_DEL_THIS_GRP"]);
        }

        DB::delete('`groups`', ['group_id'=>$group_id]);
        showMessage('admingroup/groups', LANG["DEL_COMPLETE"]);
    }

    // Admin Add/Edit Group
    public static function insertOrUpdate()
    {
        $group_id = Validate::Input('group_id', 'int');
        if (!$group_id) {
            DB::insert('`groups`', [
                'level' => $_POST["level"], 'Color' => $_POST["group_color"], 'group_role_id' => $_POST["group_role_id"], 'maxslots' => $_POST["maxslots"],
                'control_panel' => $_POST["control_panel"], 'staff_page' => $_POST["staff_page"], 'staff_public' => $_POST["staff_public"], 'staff_sort' => intval($_POST['staff_sort'])
            ]);
            showMessage('admingroup', LANG["SUCCESS"]);
        } else {
            DB::update('`groups`',
               ['level','Color','group_role_id','maxslots','control_panel','staff_page','staff_public','staff_sort'], 
               ['group_id='], 
               [$_POST["level"], $_POST["group_color"], $_POST["group_role_id"], $_POST["maxslots"], $_POST["control_panel"], $_POST["staff_page"], $_POST["staff_public"], intval($_POST['staff_sort']), $group_id]);
            showMessage('admingroup', LANG["UPDATE_COMPLETE"]);
        }
    }

}