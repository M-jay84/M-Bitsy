<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
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
        if (Validate::Input('do') == 'delete') {
            $ids = Validate::multiIds('ids');
            DB::deleteByIds('snatched',
                        ['last_time =', 'hnr =', 'done = '],
                         $ids,
                        `sid`,
                        [86400, 'no', 'yes']
                    );
                    showMessage(('adminsnatch'), LANG["DEL_COMPLETE"]);
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
    public static function submitTrade($current_user)
    {
        $torid = Validate::Input('torid');
        $torsize = Validate::Input('size');
        if ($_POST["requestpoints"]) {
            $modcomment = date("d-M-Y") . " - " . LANG["DELETED_RECORDING"] . ": " . $torid . " with " . $torsize . " " . LANG["POINTS_OF_SEED_BONUS"];
            DB::run("UPDATE users SET seedbonus = seedbonus - ?, modcomment = ? WHERE id = ?", [$torsize, $modcomment, $current_user->id]);
            SnatchedModel::update(['last_time','hnr','done'], ['tid=', 'uid='], [86400, 'no', 'yes', $torid,  $current_user->id]);
            LogsModel::write("<a href=" . URLROOT . "/profile/" . $current_user->id . "><b>" . $current_user->username . "</b></a> " . LANG["DELETED_RECORDING"] . ": <a href=" . URLROOT . "/torrent/$torid><b>$torid</b></a>  " . LANG["HIT_AND_RUN_WITH"] . " <b>$torsize</b> " . LANG["POINTS_OF_SEED_BONUS"] . "");
            showMessage(('snatch/trade'), LANG["DEL_COMPLETE"]);
        } elseif ($_POST["requestupload"]) {
            $modcomment = date("d-M-Y") . " - " . LANG["DELETED_RECORDING"] . ": " . $torid . " with " . mksize($torsize) . " " . LANG["OF_UPLOAD"];
            DB::run("UPDATE users SET uploaded = uploaded - '$torsize', modcomment = ? WHERE id = ?", [$modcomment, $current_user->id]);
            SnatchedModel::update(['last_time','hnr','done'], ['tid=', 'uid='], [86400, 'no', 'yes', $torid,  $current_user->id]);
            LogsModel::write("<a href=" . URLROOT . "/profile/" . $current_user->id . "><b>" . $current_user->username . "</b></a> " . LANG["DELETED_RECORDING"] . ": <a href=" . URLROOT . "/torrent/$torid><b>$torid</b></a> " . LANG["HIT_AND_RUN_WITH"] . " <b>$torsize</b> " . LANG["OF_UPLOAD"] . "");
            showMessage(('snatch/trade'), LANG["DEL_COMPLETE"]);
        } else {
            showMessage(('snatch/trade'), LANG["ACTION_FAILED"]);
        }
    }

}