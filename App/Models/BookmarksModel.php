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
    public function getAll($id)
    {
        $count = DB::fetchColumn('bookmarks', 'COUNT(id)', ['userid=', 'type='], [$id, 'torrent']);
        list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'] . "/bookmark/$id?");
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

    // Show Bookmark //todo html
    public function select($target, $data = [], $type = 'torrent')
    {
        $bookt = DB::fetchColumn('bookmarks', 'COUNT(id)', ['targetid=', 'type=', 'userid='], [$target, $type, $data['current_user']->id]);
        if ($bookt > 0) {
            $button = "<a href=" . CONFIG['URLROOT'] . "/bookmark/action/$target/1>" . LANG["UNBOOKMARK"] . "</a>";
        } else {
            $button = "<a href=" . CONFIG['URLROOT'] . "/bookmark/action/$target>" . LANG["BOOKMARK"] . "</a>";
        }
        return $button;
    }
		
    // Add/Delete Bookmark
    public function action($target, $del = 0, $data = [])
    {
        // Check Input
        if (!$target) {
            $ids = multiIds("ids");
            DB::deleteByIds('bookmarks', 'id', $ids);
        }

        $type = 'torrent';
        $arr = DB::fetchColumn('bookmarks', 'COUNT(id)', ['targetid=', 'type=', 'userid='], [$target, $type, $data['current_user']->id]);

        if (!$arr && $del) {
            showMessage(Url(), LANG["NOTHING_FOUND"]);
        } elseif ($arr > 0 && !$del) {
            showMessage(Url(), LANG["ALREADY_THERE"]);
        }

        if ($del) {
            // Delete Bookmark
            DB::delete('bookmarks', ['targetid' => $target, 'type' => $type, 'userid' => $data['current_user']->id]);
            if ($type === 'torrent') {
                showMessage(Url("profile/" . $data['current_user']->id . ""), LANG["DEL_COMPLETE"]);
            } else {
            }
        } else {
            // Add Bookmark
            if ($type === 'torrent') {
                $num = (new TorrentsModel)->fetchColumn('count(id)', ['id='], [$target]);
                if ($num > 0) {
                    DB::insert('bookmarks', ['userid' => $data['current_user']->id, 'targetid' => $target, 'type' => 'torrent']);
                    showMessage(Url("torrent/$target"), LANG["ITEM_BOOKMARKED"]);
                }
            } else {
                // if type forum ???
            }
        }

        showMessage(Url(), LANG["SUCCESS"]);
    }
}