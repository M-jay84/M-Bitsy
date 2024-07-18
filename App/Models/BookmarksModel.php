<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\TorrentsModel;

/*
* Bookmark Table Data
*/
class BookmarksModel
{
    // Get Bookmarks
    public static function getAll($id)
    {
        $count = DB::fetchColumn('bookmarks', 'COUNT(id)', ['userid=', 'type='], [$id, 'torrent']);
        list($pager, $limit) = pagination(25, $count, URLROOT . "/bookmark/$id?");
        $query = DB::run(
            "SELECT bookmarks.id as bookmarkid,
            torrents.size,
            torrents.freeleech,
            torrents.external,
            torrents.id,
            torrents.category,
            torrents.name,
            torrents.filename,
            torrents.added,
            torrents.banned,
            torrents.comments,
            torrents.seeders,
            torrents.leechers,
            torrents.times_completed,
            categories.name AS cat_name,
            categories.parent_cat AS cat_parent,
            categories.image AS cat_pic
            FROM bookmarks
            LEFT JOIN torrents ON bookmarks.targetid = torrents.id
            LEFT JOIN categories ON category = categories.id
            WHERE bookmarks.userid = ?
            ORDER BY added DESC 
            $limit",
            [$id]
        );
        $arr = [
            'query' => $query,
            'count' => $count,
            'pager' => $pager
        ];
        return $arr;
    }

    // Show Bokkmark
    public static function select($target, $type = 'torrent')
    {
        $bookt = DB::fetchColumn('bookmarks', 'COUNT(id)', ['targetid=', 'type=', 'userid='], [$target, $type, $_SESSION['id']]);
        if ($bookt > 0) {
            $button = "<a href=" . URLROOT . "/bookmark/action/$target/1>" . Txt("UNBOOKMARK") . "</a>";
        } else {
            $button = "<a href=" . URLROOT . "/bookmark/action/$target>" . Txt("BOOKMARK") . "</a>";
        }
        return $button;
    }
			
    // Confirm User Reg
    public static function deleteByIds($ids)
    {
        $res = DB::deleteByIds('bookmarks', 'id', $ids);
        return $res;
    }
			
    // Add/Delete Bookmark
    public static function action($target, $del = 0)
    {
        // Check Input
        if (!$target) {
            $ids = multiIds("ids");
            BookmarksModel::deleteByIds($ids);
        }

        $type = 'torrent';
        $arr = DB::fetchColumn('bookmarks', 'COUNT(id)', ['targetid=', 'type=', 'userid='], [$target, $type, $_SESSION['id']]);

        if (!$arr && $del) {
            Redirect(Url(), Txt("NOTHING_FOUND"));
        } elseif ($arr > 0 && !$del) {
            Redirect(Url(), Txt("ALREADY_THERE"));
        }

        if ($del) {
            // Delete Bookmark
            DB::delete('bookmarks', ['targetid' => $target, 'type' => $type, 'userid' => $_SESSION['id']]);
            if ($type === 'torrent') {
                Redirect(Url("profile/" . $_SESSION['id'] . ""), Txt("DEL_COMPLETE"));
            } else {
            }
        } else {
            // Add Bookmark
            if ($type === 'torrent') {
                $num = TorrentsModel::fetchColumn('count(id)', ['id='], [$target]);
                if ($num > 0) {
                    DB::insert('bookmarks', ['userid' => $_SESSION['id'], 'targetid' => $target, 'type' => 'torrent']);
                    Redirect(Url("torrent/$target"), Txt("ITEM_BOOKMARKED"));
                }
            } else {
                // if type forum ???
            }
        }

        Redirect(Url(), Txt("SUCCESS"));
    }
}