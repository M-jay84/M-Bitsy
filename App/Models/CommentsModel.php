<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\NewsModel;
use App\Models\TorrentsModel;
use App\Models\RequestsModel;
use App\Models\LogsModel;

/*
* Comments Table Data
*/
class CommentsModel
{
    // Fetch Comment Column
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('comments', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Torrent Details
    public function delete($id, $userid, $type = '')
    {
        if ($type == "torrent") {
            $row = DB::fetchRow('comments', 'torrent', ['id ='], $id);
            if ($row["torrent"] > 0) {
                (new TorrentsModel)->updateComments($id, 'sub');
            }
        }
        
        DB::delete('comments', ['id' => $id]);
        (new LogsModel)->write(coloredname($userid) . " has deleted comment: ID: $id");
        showMessage(Url($_SERVER['HTTP_REFERER']), LANG["DEL_COMPLETE"]);
	}

    // Admin Get All
    public function grabAll()
    {
        // Pagination
        $count = DB::fetchColumn('comments', 'count(id)', '');
        list($pager, $limit) = pagination(10, $count, CONFIG['URLROOT']."/admincomment?");
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
    public function getInfo($id, $type)
    {
        if ($type == "news") {
            $row = (new NewsModel)->fetchRow('*', ['id='], [$id]);
            if (!$row) {
                showMessage(Url(CONFIG['URLROOT'] . "/comment/$id/news"), LANG["MISSING_ID"]);
            }
            $title = LANG["NEWS"];
        }

        if ($type == "torrent") {
            $row = (new TorrentsModel)->fetchRow('id, name', ['id='], [$id]);
            if (!$row) {
                showMessage(Url(), LANG["TORRENT_NOT_FOUND"]);
            }
            $title = LANG["COMMENT_FOR"] . "<a href='" . CONFIG['URLROOT'] . "/torrent/" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $row = DB::fetchAll('comments', '*', ['req='], [$id]);
            $row2 = (new RequestsModel)->fetchRow('request', ['id='], [$id]);
            $title = LANG["COMMENT_FOR"] . "<a href='" . CONFIG['URLROOT'] . "/request'>" . htmlspecialchars($row2['request']) . "</a>";
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
    public function action($targetid, $data = [], $type = '')
    {
        // Check User Input
        $comment_id = Input('comment_id', 'int');
        $text = Input('text');

        // Check Correct Input
        if (!$text) {
            showMessage(Url($_SERVER['HTTP_REFERER']), LANG["MISSING_FORM_DATA"]);
        }

        if ($type == "torrent") {
            (new TorrentsModel)->updateComments($targetid, 'add');
        }
        if ($type == "req") {
            (new RequestsModel)->update($targetid);
        }

        if (!$comment_id) {
            // Insert Comment
            $ins = DB::insert('comments', ['user' => $data['current_user']->id, $type => $targetid, 'added' => get_date_time(), 'text' => $text, 'type' => $type]);
            if ($ins) {
                showMessage(Url($_SERVER['HTTP_REFERER']), LANG["SUCCESS"]);
            } else {
                showMessage(Url($_SERVER['HTTP_REFERER']), LANG["ACTION_FAILED"]);
            }
        } else {
            DB::update('comments', ['text'], ['id='], [$text, $comment_id]);
            (new LogsModel)->write(coloredname($data['current_user']->id) . " has edited comment: ID:$comment_id");
            showMessage(Url($_SERVER['HTTP_REFERER']), LANG["UPDATE_COMPLETE"]);
        }
    }
}