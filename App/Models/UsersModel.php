<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Validate;
use App\Models\GroupsModel;
use App\Models\ReadModel;
use App\Models\ReportsModel;
use App\Models\WarningsModel;
use PDO;

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
        showMessage('adminconfirmreg', LANG["ACTION_COMPLETE"]);
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
        ReportsModel::delete(['addedby' => $userid]);
        ReportsModel::delete(['votedfor' => $userid, 'type' => 'user']);
        ReadModel::delete(['userid' => $userid]);
        DB::delete('pollanswers', ['userid' => $userid]);
        DB::delete('snatched', ['uid' => $userid]);
    }

    // Message ajax member search
    public static function getUserName()
    {
        $stmt = DB::run("SELECT id,username FROM users WHERE username like '" . $_POST["keyword"] . "%' ORDER BY username LIMIT 0,6");
		return $stmt;
	}
	
    // Message ajax member search
    public static function usersToConfirm()
    {
        $count = DB::fetchColumn('users', 'count(id)', ['status =', 'invited_by ='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, 'adminconfirmreg?');
        $users = DB::newSelect('users', 'id,username,email,added,ip', ['status =','invited_by ='], ['pending',0], "ORDER BY `added` DESC $limit");
        
        $stmt = ['users'=>$users,'count'=>$count,'pager'=>$pager];
		return $stmt;
	}
    public static function inactiveUsers()
    {
        $cday = Validate::Input('cday', 'int') ?? 30;
        $dt = date("Y-m-d H:i:s", CONFIG['TT_TIME'] - ($cday * 86400));
        
        // Get Users Data
        $users = DB::newSelect('users', 'id,username,class,email,uploaded,downloaded,last_access,ip,added', 
                                ['last_access<','status=','enabled='] , [$dt, 'confirmed', 'yes'], "ORDER BY last_access DESC");
        $count = $users->rowCount();
        $stmt = ['users'=>$users,'count'=>$count,'days'=>$cday];
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

    public static function inactiveUsersSubmit($username)
    {
        $sitename = CONFIG['SITENAME'];
        $siteurl = URLROOT;
        
            // Check User Input
            $action = Validate::Input('action');
            $cday = Validate::Input('cday', 'int');
            $days = 30; // Number of days of inactivite

            // Check Correct Input
            if (!is_numeric($cday)) {
                showMessage('admininactive', LANG["ACTION_FAILED"]);
            }

            if (empty($_POST["userid"]) && (($action == "deluser") || ($action == "mail") || ($action == "disable"))) {
                showMessage('admininactive', ("NOTHING_SELECTED"));
            }

            if ($action == "cday" && ($cday > $days)) {
                $days = $cday;
            }

            // Delete
            if ($action == "deluser" && (!empty($_POST["userid"]))) {
                $ids = implode(", ", $_POST['userid']);
                DB::deleteByIds('users', 'id', $ids);
                showMessage('admininactive', LANG["SUCCESS"]);
            }

            // Disable
            if ($action == "disable" && (!empty($_POST["userid"]))) {
                $res = UsersModel::selectWhereIn();
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = 0 + $arr["id"];
                    $cname = $username;
                    $modcomment = $arr["modcomment"];
                    $modcomment = date("Y-m-d") . " - Disabled for inactivity by $cname.n" . $modcomment;
                    DB::update('users', ['modcomment', 'enabled'], ['id='], [$modcomment,'no',$id]);
                }
                showMessage('admininactive', LANG["UPDATE_COMPLETE"]);
            }

            // Email
            if ($action == "mail" && (!empty($_POST["userid"]))) {
                $res = UsersModel::selectWhereIn(true);
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = $arr["id"];
                    $username = htmlspecialchars($arr["username"]);
                    $email = htmlspecialchars($arr["email"]);
                    $added = $arr["added"];
                    $last_access = $arr["last_access"];
                    $subject = "Warning for inactive account on $sitename";

                    // Send Email
                    $body = file_get_contents(VIEW_PATH . "/user/email/changeemail.php");
                    $body = str_replace("%username%", $username, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $body = str_replace("%siteurl%", $siteurl, $body);

                    $body = str_replace("%added%", $$added, $body);
                    $body = str_replace("%last_access%", $last_access, $body);
                    $TTMail = new TTMail();
                    $mail = $TTMail->Send($email, $subject, $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
                }

                if ($mail) {
                    showMessage('admininactive', "Messages sent.");
                } else {
                    showMessage('admininactive',  "Try again.");
                }

            }
	}
}