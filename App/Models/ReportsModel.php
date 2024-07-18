<?php

namespace App\Models;

use App\Libs\DB;

/*
* Report Table Data
*/
class ReportsModel
{
    // Admin Get Reports
    public static function pagination()
    {
        $pager = array();
        $where = array();
        
        switch ($_GET["type"]) {
            case "user":
                $where[] = "type = 'user'";
                $pager[] = "type=user";
                break;
            case "torrent":
                $where[] = "type = 'torrent'";
                $pager[] = "type=torrent";
                break;
            case "comment":
                $where[] = "type = 'comment'";
                $pager[] = "type=comment";
                break;
            case "forum":
                $where[] = "type = 'forum'";
                $pager[] = "type=forum";
                break;
            case "req":
                $where[] = "type = 'req'";
                $pager[] = "type=req";
                break;
            default:
                break;
        }

        switch ($_GET["completed"]) {
            case 1:
                $where[] = "complete = '1'";
                $pager[] = "complete=1";
                break;
            default:
                $where[] = "complete = '0'";
                $pager[] = "complete=0";
                break;
        }

        $where = implode(" AND ", $where);
        $pager = implode("&amp;", $pager);
        
        $switch = ['where'=>$where,'pager'=>$pager];

        // Pagination
        if ($switch['where'] != '') {
            $count = DB::run("SELECT count(id) FROM reports WHERE $switch[where]")->fetchColumn();
            list($pager, $limit) = pagination(25, $count, "$switch[pager]&amp;");
            $stmt = DB::run("SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra, reports.reason, reports.type, users.username, reports.complete 
                            FROM `reports` INNER JOIN users ON reports.addedby = users.id 
                            WHERE $switch[where] ORDER BY reports.id DESC $limit")->fetchAll();
        } else {
            $count = DB::fetchColumn('reports', 'count(id)', '');
            list($pager, $limit) = pagination(25, $count, "$switch[pager]&amp;");
            $stmt = DB::run("SELECT reports.id, reports.dealtwith, reports.dealtby, reports.addedby, reports.votedfor, reports.votedfor_xtra, reports.reason, reports.type, users.username, reports.complete 
                            FROM `reports` INNER JOIN users ON reports.addedby = users.id 
                            ORDER BY reports.id DESC $limit")->fetchAll();         
        }

        $array = ['stmt'=>$stmt,'pager'=>$pager];
        return $array;
    }

    // Admin Update Report
    public static function update()
    {
        $ids = multiIds('ids');
        DB::run("UPDATE reports SET complete = '1', dealtwith = '1', dealtby = ".$_SESSION['id']." WHERE id IN ($ids)");
        Redirect(Url('adminreport'), Txt("ACTION_COMPLETE"));
     }

    // Admin Delete Report
    public static function deleteByIds()
    {
        
        $ids = multiIds('ids');
        $stmt = DB::deleteByIds('reports', ['complete ='], $ids , 'id', [1]);
        Redirect(Url('adminreport'), Txt("SUCCESS"));
    }
	
    // Delete Report
    public static function delete($where)
    {
        $stmt = DB::delete('reports', $where);
        return $stmt;
    }
	
    // Add Report
    public static function reportItem($USER)
    {
        // Check User Input
        $id = Input('id', 'int');
        $type = Input('type', 'int');
        $reason = Input('reason');
        $postid = Input('postid', 'int');

        // Check User
        if (!$USER->permission('view_users', $USER->has('id')) && $type == 'user') {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        } elseif (!$USER->permission('view_torrents') && $type == 'torrent') {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        } elseif (!$USER->permission('view_forum') && $type == 'forum') {
            Redirect(Url(), Txt("FORUM_BANNED"));
        }

        if (!empty($_POST)) {
            if (empty($reason) || empty($id) &&  empty($postid)) {
                Redirect($_SERVER['HTTP_REFERER'], Txt("YOU_MUST_ENTER_A_REASON"));
            }

            $res = DB::fetchColumn('reports', 'count(id)', ['addedby=', 'votedfor=', 'type='], [$USER->has('id'), $id, $type]);
            if (!$res) {
                if ($type == 'forum') {
                    DB::insert('reports', ['addedby' => $USER->has('id'), 'votedfor' => $id, 'votedfor_xtra' => $postid, 'type' => $type, 'reason' => $reason]);
                } elseif ($type == 'other' || $type == 'req' || $type == 'comment') {
                    DB::insert('reports', ['addedby' => $USER->has('id'), 'votedfor' => $postid, 'votedfor_xtra' => 0, 'type' => $type, 'reason' => $reason]);
                } else {
                    DB::insert('reports', ['addedby' => $USER->has('id'), 'votedfor' => $id, 'votedfor_xtra' => 0, 'type' => $type, 'reason' => $reason]);
                }
                    Redirect($_SERVER['HTTP_REFERER'], "$type: $id, $postid Reason: " . htmlspecialchars($reason) . "<p>Successfully Reported</p>");
            } else {
                Redirect($_SERVER['HTTP_REFERER'], Txt("YOU_HAVE_ALREADY_REPORTED"));
            }
        }
    }

}