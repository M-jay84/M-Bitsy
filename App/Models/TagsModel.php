<?php

namespace App\Models;

use App\Libs\DB;
use PDO;

/*
*  Tags Table Data
*/
class TagsModel
{
  // Get Tag Names
  public static function getAll()
  {
    $stmt = DB::fetchAll('tags', 'id, name, colour', '', 'GROUP BY `name`');
    return $stmt;
  }

  // Admin Add/Update Tag
  public static function insertOrUpdate()
  {
    $name = Input('name');
    $colour = Input('colour');
    $id = Input('id', 'int');

    if (!$name) {
      Redirect(Url('admintag'), Txt("MISSING_FORM_DATA"));
    }

    if (!$id) {
      DB::insert('tags', ['name' => $name, 'colour' => $colour]);
      Redirect(Url('admintag'), Txt('SUCCESS'));
    } else {
      $stmt = DB::update('tags', ['name', 'colour'], ['id='], [$name,$colour,$id]);
      return $stmt;
    }
    
    Redirect(Url('admintag'), Txt("ACTION_COMPLETE"));
  }

  // Admin Delete Tag
  public static function delete()
  {
    // Check User Input
    $ids = multiIds('ids');
    DB::deleteByIds('tags', 'id', $ids);
    Redirect(Url('admintag'), Txt("DEL_COMPLETE"));
  }

  // Search By Tag
  public static function search($name)
  {
    // Get Tag Data
    $stmt = DB::run("SELECT torrentid FROM tagtorrent WHERE type1 = ? OR type2 = ? OR type3 = ?", [$name, $name, $name])->fetchAll();
    if (count($stmt) == 0) {
      Redirect(Url(), Txt("NOTHING_FOUND"));
    }

    // String Of IDS
    $array = array();
    foreach ($stmt as $item) {
      array_push($array, $item['torrentid']);
    }
    $ids = implode(",", $array);
    $prefix = ',';
    if (substr($ids, 0, strlen($prefix)) == $prefix) {
      $ids = substr($ids, strlen($prefix));
    }

    // Get Torrent Data
    $count = DB::run("SELECT COUNT(*) FROM torrents WHERE torrents.id IN ($ids)")->fetchColumn();
    list($pager, $limit) = pagination(25, $count, URLROOT . "/tag/$name&amp;");

    $stmt = DB::run(
      "SELECT torrents.anon, torrents.seeders, torrents.tube, 
                    torrents.banned, torrents.leechers, torrents.info_hash, 
                    torrents.filename, torrents.nfo, torrents.last_action, 
                    torrents.numratings, torrents.name, torrents.tmdb, 
                    torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, 
                    torrents.visible, torrents.size, torrents.added, torrents.views, 
                    torrents.hits, torrents.times_completed, torrents.id, torrents.type, 
                    torrents.external, torrents.image1, torrents.image2, torrents.announce, 
                    torrents.numfiles, torrents.freeleech, torrents.vip, torrents.category, torrents.sticky,
                    torrents.announcelist, torrents.filelist,
                    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, 
                    torrents.numratings, 
                    categories.name AS cat_name, 
                    torrentlang.name AS lang_name, 
                    torrentlang.image AS lang_image, 
                    categories.parent_cat as cat_parent, 
                    users.username, users.privacy 
            FROM torrents 
            LEFT JOIN categories ON torrents.category = categories.id 
            LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
            LEFT JOIN users ON torrents.owner = users.id
            WHERE torrents.id IN ($ids) $limit"
    )->fetchAll(PDO::FETCH_ASSOC);
    if (count($stmt) == 0) {
      Redirect(Url(), Txt("NO_TORRENT_FOUND"));
    }
    $array = [
      'stmt' => $stmt,
      'pager' => $pager,
      'count' => $count
    ];

    return $array;
  }
}