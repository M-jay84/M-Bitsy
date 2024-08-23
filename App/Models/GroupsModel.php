<?php

namespace App\Models;

use App\Libs\DB;
use PDO;

/*
* Groups Table Data
*/
class GroupsModel
{
    // Fetch Groups Level
    public function fetchlevel($ids)
    {
        $stmt = DB::run("SELECT DISTINCT level FROM `groups` WHERE group_id IN ($ids)")->fetchAll();
		return $stmt;
	}
		
    // Fetch All Groups
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('groups', $columns, $where, $values, $other);
		return $stmt;
	}

    // Get Staff
    public function getStaffTable($data = [])
    {
        $dt = get_date_time(gmtime() - 180);

        // Get Staff Data
        $res = DB::run("SELECT `users`.`id`, `users`.`username`, `users`.`class`, `users`.`last_access`
        FROM `users`
        INNER JOIN `groups`
        ON `users`.`class` = `groups`.`group_id`
        WHERE `users`.`enabled` =? AND `users`.`status` =? AND `groups`.`staff_page` =?
        ORDER BY `username`",
        ['yes', 'confirmed', 'yes']);
        $col = [];
        $table = [];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $table[$row['class']] = ($table[$row['class']] ?? '') .
            "<td><i class='fa fa-user' aria-hidden='true' title='Profile'></i>
                 <a href='" . CONFIG['URLROOT'] . "/profile/$row[id]'>" . coloredname($row["id"]) . "</a>
                 <a href='" . CONFIG['URLROOT'] . "/message/create/$row[id]'><i class='fa fa-comment' title='Send PM'></i></a></td>";
            $col[$row['class']] = ($col[$row['class']] ?? 0) + 1;
            if ($col[$row["class"]] <= 4) {
                $table[$row["class"]] = $table[$row["class"]] . "<td></td>";
            } else {
                $table[$row["class"]] = $table[$row["class"]] . "</tr><tr>";
                $col[$row["class"]] = 2;
            }
        }

        $where = null;
        if (!$data['current_user']->edit_users == 'yes') {
            $where = "AND `staff_public` = 'yes'";
        }

        $res = DB::run("SELECT `group_id`, `level`, `staff_public`
        FROM `groups`
        WHERE `staff_page` = 'yes' $where
        ORDER BY `staff_sort`");
        if ($res->rowCount() == 0) {
            showMessage(Url(), LANG["NO_STAFF_HERE"]);
        }

        $array = [
            'res' => $res,
            'table' => $table
        ];

        return $array;
    }

    // Function That Returns The Group Name
    public function GetUserClassName($i, $data = [])
    {
        $row = DB::fetchRow('`groups`', 'level', ['group_id ='], [$i]);
        if ($i == $data['current_user']->class) {
           return  $row['level'];
        }
        return $row['level'];
    }

    // Function To List Groups Of Members Of The Database
    public function getGroups()
    {
        $stmt = DB::run("SELECT * FROM `groups` 
                         LEFT OUTER JOIN `permissions` 
                         ON `groups`.group_role_id=`permissions`.permission_id")->fetchAll();
        return $stmt;
    }

    // Admin Delete Group
    public function delete($group_id)
    {
        // Protect Groups
        if (($group_id == "1") || ($group_id == CONFIG['_ADMINISTRATOR'])) {
            showMessage(Url('admingroup'), LANG["YOU_CANT_DEL_THIS_GRP"]);
        }

        DB::delete('`groups`', ['group_id'=>$group_id]);
        showMessage(Url('admingroup/groups'), LANG["DEL_COMPLETE"]);
    }

    // Admin Add/Edit Group
    public function insertOrUpdate()
    {
        $group_id = Input('group_id', 'int');
        if (!$group_id) {
            DB::insert('`groups`', [
                'level' => $_POST["level"], 'Color' => $_POST["group_color"], 'group_role_id' => $_POST["group_role_id"], 'maxslots' => $_POST["maxslots"],
                'control_panel' => $_POST["control_panel"], 'staff_page' => $_POST["staff_page"], 'staff_public' => $_POST["staff_public"], 'staff_sort' => intval($_POST['staff_sort'])
            ]);
            showMessage(Url('admingroup'), LANG["SUCCESS"]);
        } else {
            DB::update('`groups`',
               ['level','Color','group_role_id','maxslots','control_panel','staff_page','staff_public','staff_sort'], 
               ['group_id='], 
               [$_POST["level"], $_POST["group_color"], $_POST["group_role_id"], $_POST["maxslots"], $_POST["control_panel"], $_POST["staff_page"], $_POST["staff_public"], intval($_POST['staff_sort']), $group_id]);
            showMessage(Url('admingroup'), LANG["UPDATE_COMPLETE"]);
        }
    }

}