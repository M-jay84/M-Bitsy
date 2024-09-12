<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\ReadModel;
use App\Models\ReportsModel;
use App\Models\GroupsModel;
use App\Models\WarningsModel;

/*
* Users Table Data
*/
class UsersModel
{
    // Admin Confirm User Reg
    public static function confirmReg()
    {
        $ids = Validate::multiIds('ids');
        DB::run("UPDATE `users` SET `status` = ? WHERE `status` = ? AND `invited_by` = ? AND `id` IN (?)", ['confirmed', 'pending', 0, $ids])->fetchAll();
        showMessage(('adminconfirmreg'), LANG["ACTION_COMPLETE"]);
    }

    // Get Members
    public static function getMembers()
    {
        // Check User Input
        $search = (isset($_GET['search'])) ? trim(filter_input(INPUT_GET, 'search')) : '';
        $class = (int) (isset($_GET['class'])) ? $_GET['class'] : 0;
        $letter = (isset($_GET['letter'])) ? trim(filter_input(INPUT_GET, 'letter')) : '';

        // Search Users
        $q = $query = null;
        if ($search) {
            $query = "username LIKE '$search%' AND status='confirmed'";
            if ($search) {
                $q = "search=" . htmlspecialchars($search);
            }
        } elseif ($letter) {
            if (strlen($letter) > 1) {
                unset($letter);
            }
            if ($letter == "" || strpos("abcdefghijklmnopqrstuvwxyz", $letter) === false) {
                unset($letter);
            } else {
                $query = "username LIKE '$letter%' AND status='confirmed'"; // todo esc
            }
            $q = "letter=$letter";
        }
        if (!$query) {
            $query = "status='confirmed'";
        }
        if (!$class) {
            unset($class);
        } else {
            $query .= " AND class=$class";
            $q .= ($q ? "&amp;" : "") . "class=$class";
        }

        $data = ['query' => $query, 'q' => $q];
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM users WHERE $data[query]")->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, URLROOT . "/members?$data[q]&");
        $results = DB::run("SELECT users.*, groups.level FROM users INNER JOIN `groups` ON groups.group_id=users.class WHERE $data[query] ORDER BY username $limit")->fetchAll();

        // Get Group Data
        $res = GroupsModel::fetchAll('group_id, level', '', 'ORDER BY group_id ASC');

        $array = [
            'groups' => $res,
            'array' => $results,
            'pager' => $pager
        ];

        return $array;
    }

    // Function That Removes All From An Account
    public static function deleteUser($userid)
    {
        DB::delete('users', ['id' => $userid]);
        WarningsModel::delete(['userid' => $userid]);
        DB::delete('ratings', ['user' => $userid]);
        DB::delete('peers', ['userid' => $userid]);
        DB::delete('completed', ['userid' => $userid]);
        ReportsModel::delete(['addedby' => $userid]);
        ReportsModel::delete(['votedfor' => $userid, 'type' => 'user']);
        ReadModel::delete($userid);
        DB::delete('pollanswers', ['userid' => $userid]);
        DB::delete('snatched', ['uid' => $userid]);
    }

    // Message ajax member search
    public static function getUserName()
    {
        $stmt = DB::run("SELECT id,username FROM users WHERE username like '" . $_POST["keyword"] . "%' ORDER BY username LIMIT 0,6");
		return $stmt;
	}
		
    // User whew inactive?
    public static function selectWhereIn($option = false)
    {
        if ($option) {
			$stmt = DB::run("SELECT id, email , username, added, last_access FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY last_access DESC ");
		} else {
			$stmt = DB::run("SELECT id, modcomment FROM users WHERE id IN (" . implode(", ", $_POST['userid']) . ") ORDER BY id DESC ");
		}
		return $stmt;
	}

}