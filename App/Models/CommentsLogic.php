<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsLogic;
use App\Models\NewsLogic;
use App\Models\TorrentsLogic;
use PDO;

/*
* Comments Table Data
*/
class CommentsLogic
{
    // Fetch Torrent Details
    public static function delete($id, $userid, $type = '')
    {
        if ($type == "torrent") {
            $torrents_query = "SELECT torrent FROM `comments`  WHERE id=?";
            $row = DB::run($torrents_query, [$id])->fetch(PDO::FETCH_ASSOC);
            if ($row["torrent"] > 0) {
                TorrentsLogic::updateComments($id, 'sub');
            }
        }
        $comments_query = "DELETE FROM `comments` WHERE id' = ?";
        DB::run($comments_query, [$id]);
        LogsLogic::write(coloredname($userid) . " has deleted comment: ID: $id");
        showMessage("comment", LANG["DEL_COMPLETE"]);
	}

    // Admin Get All
    public static function grabAll()
    {
        // Pagination
        $comments_query = "SELECT count(id) FROM `comments`";
        $count = DB::run($comments_query)->fetchColumn();
        list($pager, $limit) = pagination(10, $count, URLROOT."/admincomment?");
        $comments_query = "SELECT c.id, c.text, c.user, c.torrent, c.news, t.name, n.title, u.username, c.added 
        FROM comments c 
        LEFT JOIN torrents t ON c.torrent = t.id 
        LEFT JOIN news n ON c.news = n.id 
        LEFT JOIN users u ON c.user = u.id 
        ORDER BY c.added DESC $limit";
        $stmt = DB::run($comments_query)->fetchAll();
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
            $news_query = "SELECT * FROM `news`  WHERE id=?";
            $row = DB::run($news_query, [$id])->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                showMessage((URLROOT . "/comment/$id/news"), LANG["MISSING_ID"]);
            }
            $title = LANG["NEWS"];
        }

        if ($type == "torrent") {
            $torrents_query = "SELECT id,name FROM `torrents` WHERE id=?";
            $row = DB::run($torrents_query, [$id])->fetch(PDO::FETCH_ASSOC);
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
        $comments_query = "SELECT count(id) FROM `comments` WHERE type=?";
        $commcount = DB::run($comments_query, [$id])->fetchColumn();
        if ($commcount) {
            list($pager, $limit) = pagination(10, $commcount, "comments?id=$id&amp;type=$type");
            $comments_query = "SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit";
            $commres = DB::run($comments_query);
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
            showMessage("comment/$targetid/$type", LANG["MISSING_FORM_DATA"]);
        }

        if ($type == "torrent") {
            TorrentsLogic::updateComments($targetid, 'add');
        }

        if (!$comment_id) {
            // Insert Comment
            $comments_query = "INSERT INTO `comments` (user,$type,added,text,type) VALUES (?,?,?,?,?)";
            $ins =  DB::run($comments_query,
            [$id,$targetid, CONFIG['TT_DATE'], $text, $type]);
            
            if ($ins) {
                showMessage("comment/$targetid/$type", LANG["SUCCESS"]);
            } else {
                showMessage("comment/$targetid/$type", LANG["ACTION_FAILED"]);
            }
        } else {
            $comments_query = "UPDATE comments SET text = ? WHERE id=?";
            DB::run($comments_query, [$text, $comment_id]);
            LogsLogic::write(coloredname($id) . " has edited comment: ID:$comment_id");
            showMessage("comment/$targetid/$type", LANG["UPDATE_COMPLETE"]);
        }
    }
}