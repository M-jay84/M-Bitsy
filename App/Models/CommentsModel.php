<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\NewsModel;
use App\Models\TorrentsModel;
use App\Models\LogsModel;

/*
* Comments Table Data
*/
class CommentsModel
{
    // Fetch Comment Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('comments', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Torrent Details
    public static function delete($id, $userid, $type = '')
    {
        if ($type == "torrent") {
            $row = DB::fetchRow('comments', 'torrent', ['id ='], $id);
            if ($row["torrent"] > 0) {
                TorrentsModel::updateComments($id, 'sub');
            }
        }
        
        DB::delete('comments', ['id' => $id]);
        LogsModel::write(coloredname($userid) . " has deleted comment: ID: $id");
        showMessage(($_SERVER['HTTP_REFERER']), LANG["DEL_COMPLETE"]);
	}

    // Admin Get All
    public static function grabAll()
    {
        // Pagination
        $count = DB::fetchColumn('comments', 'count(id)', '');
        list($pager, $limit) = pagination(10, $count, URLROOT."/admincomment?");
        $stmt = DB::run("SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added 
        FROM comments c 
        LEFT JOIN torrents t ON c.torrent = t.id 
        LEFT JOIN news n ON c.news = n.id 
        LEFT JOIN users u ON c.user = u.id 
        ORDER BY c.added DESC $limit")->fetchAll();
        $array = [
            'pager' => $pager,
            'stmt' => $stmt,
            'count' => $count
        ];
        return $array;
    }

    // Get Comment
    public static function getInfo($id, $type)
    {
        if ($type == "news") {
            $row = NewsModel::fetchRow('*', ['id='], [$id]);
            if (!$row) {
                showMessage((URLROOT . "/comment/$id/news"), LANG["MISSING_ID"]);
            }
            $title = LANG["NEWS"];
        }

        if ($type == "torrent") {
            $row = TorrentsModel::fetchRow('id, name', ['id='], [$id]);
            if (!$row) {
                showMessage(URLROOT, LANG["TORRENT_NOT_FOUND"]);
            }
            $title = LANG["COMMENT_FOR"] . "<a href='" . URLROOT . "/torrent/" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "id") {
            $title = LANG["COMMENT_FOR"] . " " . coloredname($id);
            $type = "comments.user";
        }

        // Get Comments Data
        $commcount = DB::fetchColumn('comments', 'COUNT(id)', ["$type ="], [$id]);
        if ($commcount) {
            list($pager, $limit) = pagination(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        $pager = [
            'res' => $commres,
            'pager' => $pager,
            'count' => $commcount,
        ];

        $data = [
            'title' => $title,
            'row' => $row,
            'res' => $pager['res'],
            'pager' => $pager['pager'],
            'count' => $pager['count'],
        ];
        return $data;

    }

    // Add/Edit Comment
    public static function action($targetid, $id, $type = '')
    {
        // Check User Input
        $comment_id = Validate::Input('comment_id', 'int');
        $text = Validate::Input('text');

        // Check Correct Input
        if (!$text) {
            showMessage(($_SERVER['HTTP_REFERER']), LANG["MISSING_FORM_DATA"]);
        }

        if ($type == "torrent") {
            TorrentsModel::updateComments($targetid, 'add');
        }

        if (!$comment_id) {
            // Insert Comment
            $ins = DB::insert('comments', ['user' => $id, $type => $targetid, 'added' => CONFIG['TT_DATE'], 'text' => $text, 'type' => $type]);
            if ($ins) {
                showMessage(($_SERVER['HTTP_REFERER']), LANG["SUCCESS"]);
            } else {
                showMessage(($_SERVER['HTTP_REFERER']), LANG["ACTION_FAILED"]);
            }
        } else {
            DB::update('comments', ['text'], ['id='], [$text, $comment_id]);
            LogsModel::write(coloredname($id) . " has edited comment: ID:$comment_id");
            showMessage(($_SERVER['HTTP_REFERER']), LANG["UPDATE_COMPLETE"]);
        }
    }
}