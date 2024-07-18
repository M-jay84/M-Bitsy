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
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('comments', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Comment Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('comments', $columns, $where, $values, $other);
		return $stmt;
	}

    // Fetch Torrent Details
    public static function delete($id, $type = '')
    {
        if ($type == "torrent") {
            $row = CommentsModel::fetchRow('comments', 'torrent', ['id ='], $id);
            if ($row["torrent"] > 0) {
                TorrentsModel::updateComments($id, 'sub');
            }
        }
        
        DB::delete('comments', ['id' => $id]);
        LogsModel::write(coloredname($_SESSION['id']) . " has deleted comment: ID: $id");
        Redirect($_SERVER['HTTP_REFERER'], Txt("DEL_COMPLETE"));
	}

    // Get Comments
    public static function commentPager($id, $type)
    {
        $commcount = DB::fetchColumn('comments', 'COUNT(id)', ["$type ="], [$id]);
        if ($commcount) {
            list($pager, $limit) = pagination(10, $commcount, "comments?id=$id&amp;type=$type");
            $commres = DB::run("SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE $type = $id ORDER BY comments.id $limit");
        } else {
            unset($commres);
        }
        return $pager = [
            'res' => $commres,
            'pager' => $pager,
            'count' => $commcount,
        ];
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
                Redirect(URLROOT . "/comment/$id/news", Txt("MISSING_ID"));
            }
            $title = Txt("NEWS");
        }

        if ($type == "torrent") {
            $row = TorrentsModel::fetchRow('id, name', ['id='], [$id]);
            if (!$row) {
                Redirect(Url(), Txt("TORRENT_NOT_FOUND"));
            }
            $title = Txt("COMMENT_FOR") . "<a href='" . URLROOT . "/torrent/" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</a>";
        }

        if ($type == "req") {
            $row = DB::fetchAll('comments', '*', ['req='], [$id]);
            $row2 = RequestsModel::fetchRow('request', ['id='], [$id]);
            $title = Txt("COMMENT_FOR") . "<a href='" . URLROOT . "/request'>" . htmlspecialchars($row2['request']) . "</a>";
        }

        if ($type == "id") {
            $title = Txt("COMMENT_FOR") . " " . coloredname($id);
            $type = "comments.user";
        }

        // Get Comments Data
        $pager = self::commentPager($id, $type);

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
    public static function action($targetid, $type = '')
    {
        // Check User Input
        $comment_id = Input('comment_id', 'int');
        $text = Input('text');

        // Check Correct Input
        if (!$text) {
            Redirect($_SERVER['HTTP_REFERER'], Txt("MISSING_FORM_DATA"));
        }

        if ($type == "torrent") {
            TorrentsModel::updateComments($targetid, 'add');
        }
        if ($type == "req") {
            RequestsModel::update($targetid);
        }

        if (!$comment_id) {
            // Insert Comment
            $ins = DB::insert('comments', ['user' => $_SESSION['id'], $type => $targetid, 'added' => get_date_time(), 'text' => $text, 'type' => $type]);
            if ($ins) {
                Redirect($_SERVER['HTTP_REFERER'], Txt("SUCCESS"));
            } else {
                Redirect($_SERVER['HTTP_REFERER'], Txt("ACTION_FAILED"));
            }
        } else {
            DB::update('comments', ['text'], ['id='], [$text, $comment_id]);
            LogsModel::write(coloredname($_SESSION['id']) . " has edited comment: ID:$comment_id");
            Redirect($_SERVER['HTTP_REFERER'], Txt("UPDATE_COMPLETE"));
        }
    }
}