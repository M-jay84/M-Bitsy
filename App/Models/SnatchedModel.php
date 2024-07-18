<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\UsersModel;
use App\Models\LogsModel;

/*
* Snatched Table Data
*/
class SnatchedModel
{
    // Fetch Snatched Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('snatched', $columns, $where, $values, $other)->fetchColumn();
        return  $stmt;
    }

    // Fetch All Snatched
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('snatched', $columns, $where, $values, $other);
        return  $stmt;
    }

    // Update Snatched
    public static function update($columns, $where = '', $values = '', $other = '')
    {
        $stmt = DB::update('snatched', $columns, $where, $values, $other);
        return  $stmt;
    }

    // Completed Snatched
    public static function completed($id)
    {
        $stmt = DB::run(
            "SELECT users.id 
            FROM completed 
            LEFT JOIN users 
            ON completed.userid = users.id 
            WHERE users.enabled = 'yes' 
            AND users.status = 'confirmed' AND completed.torrentid = $id"
        )->fetchAll();
        return  $stmt;
    }

    // Admin Delete By IDs
    public static function deleteByIds()
    {
        // Delete
        if (Input('do') == 'delete') {
            $ids = multiIds('ids');
            DB::deleteByIds('snatched',
                        ['last_time =', 'hnr =', 'done = '],
                         $ids,
                        `sid`,
                        [86400, 'no', 'yes']
                    );
                    Redirect(Url('adminsnatch'), Txt("DEL_COMPLETE"));
                }
    }

    // Get Snatched Data
    public static function join($where, $value)
    {
        $count = DB::run("SELECT COUNT(*) FROM snatched WHERE $where", [$value])->fetchColumn();
        if ($where == 'snatched.uid = ?') {
            list($pager, $limit) = pagination(25, $count, "/snatch/$value/user&");
        } elseif ($where == 'snatched.tid = ?') {
            list($pager, $limit) = pagination(25, $count, "/snatch/$value/torrent&");
        } elseif ($where == 'snatched.tid = ? AND snatched.completed != 0') {
            list($pager, $limit) = pagination(25, $count, "/snatch/$value/complete&");
        }

        $res = DB::run(
            "SELECT
            users.id, users.username, users.class,
            snatched.uid as uid,
             snatched.tid as tid,
             snatched.sid,
             snatched.upload,
             snatched.download,
             snatched.start_time,
             snatched.upload_time,
             snatched.last_time,
             snatched.completed,
             snatched.hnr,
             torrents.name,
            (
                SELECT seeder
                FROM peers
                WHERE torrent = tid AND userid = uid
                LIMIT 1
            ) AS seeding
            FROM snatched
            INNER JOIN users ON snatched.uid = users.id
            INNER JOIN torrents ON snatched.tid = torrents.id
            WHERE users.status = 'confirmed' AND torrents.banned = 'no' AND $where
            ORDER BY start_time 
            DESC $limit",
            [$value]
        );
        //)->fetchAll();

        $data = [
            'stmt' => $res,
            'pager' => $pager,
            'count' => $count
        ];

        return $data;
    }

    // Trade Snatch
    public static function trade($uid)
    {
        $res = DB::run(
            "SELECT  snatched.tid as tid, torrents.name,  torrents.size, 
	                snatched.upload, snatched.download, snatched.last_time, 
		            snatched.hnr, users.uploaded, users.downloaded, users.seedbonus, users.modcomment 
		    FROM snatched 
		    INNER JOIN users ON snatched.uid = users.id 
		    INNER JOIN torrents ON snatched.tid = torrents.id
		    WHERE users.status = 'confirmed'
		    AND snatched.uid = '$uid'
		    AND snatched.hnr = 'yes'
		    AND snatched.done = 'no'
		    ORDER BY start_time DESC")->fetchAll();

        return $res;
    }

    // Submit Trade
    public static function submitTrade()
    {
        $torid = Input('torid');
        $torsize = Input('size');
        if ($_POST["requestpoints"]) {
            $modcomment = gmdate("d-M-Y") . " - " . Txt("DELETED_RECORDING") . ": " . $torid . " with " . $torsize . " " . Txt("POINTS_OF_SEED_BONUS");
            UsersModel::updatebonus($torsize, $modcomment, $_SESSION['id']);
            SnatchedModel::update(['last_time','hnr','done'], ['tid=', 'uid='], [86400, 'no', 'yes', $torid,  $_SESSION['id']]);
            LogsModel::write("<a href=" . URLROOT . "/profile/" . $_SESSION['id'] . "><b>" . $_SESSION['username'] . "</b></a> " . Txt("DELETED_RECORDING") . ": <a href=" . URLROOT . "/torrent/$torid><b>$torid</b></a>  " . Txt("HIT_AND_RUN_WITH") . " <b>$torsize</b> " . Txt("POINTS_OF_SEED_BONUS") . "");
            Redirect(Url('snatch/trade'), Txt("DEL_COMPLETE"));
        } elseif ($_POST["requestupload"]) {
            $modcomment = gmdate("d-M-Y") . " - " . Txt("DELETED_RECORDING") . ": " . $torid . " with " . mksize($torsize) . " " . Txt("OF_UPLOAD");
            UsersModel::updateupload($torsize, $modcomment, $_SESSION['id']);
            SnatchedModel::update(['last_time','hnr','done'], ['tid=', 'uid='], [86400, 'no', 'yes', $torid,  $_SESSION['id']]);
            LogsModel::write("<a href=" . URLROOT . "/profile/" . $_SESSION['id'] . "><b>" . $_SESSION['username'] . "</b></a> " . Txt("DELETED_RECORDING") . ": <a href=" . URLROOT . "/torrent/$torid><b>$torid</b></a> " . Txt("HIT_AND_RUN_WITH") . " <b>$torsize</b> " . Txt("OF_UPLOAD") . "");
            Redirect(Url('snatch/trade'), Txt("DEL_COMPLETE"));
        } else {
            Redirect(Url('snatch/trade'), Txt("ACTION_FAILED"));
        }
    }

}