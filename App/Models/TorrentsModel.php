<?php

namespace App\Models;

use App\Libs\DB;
use PDO;
use App\Libs\MDBS;
use App\Models\LikesModel;
use App\Models\TorrentTagModel;
use App\Models\TagsModel;
use App\Models\BookmarksModel;
use App\Models\CatagoriesModel;
use App\Models\RatingsModel;
use App\Models\ReportsModel;
use App\Models\TorrentLangModel;
use App\Models\LogsModel;
use App\Models\SnatchedModel;
use App\Models\MessagesModel;

/*
* Torrent Table Data
*/
class TorrentsModel
{
    // Fetch Torrent Details
    public function updateset($updateset, $id)
    {
		$stmt = DB::updateset("torrents", $updateset, ['id' => $id]);
        return $stmt;
	}

    // Fetch Torrent Details
    public function insert($updateset)
    {
        $stmt = DB::insert('torrents', $updateset);
        return $stmt;
	}
	
    // Fetch Torrent Details
    public function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('torrents', $cols, $where, $vals, $other);
        return $stmt;
	}
	
    // Fetch Torrent Details
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('torrents', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
	
    // Fetch Torrent Details
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('torrents', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Torrent Details
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('torrents', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch Torrent Details
    public function fetchIdDetails($where, $value, $data)
    {
        $stmt = array();
        $stmt = DB::run(
            "SELECT torrents.anon, torrents.seeders, torrents.tube, 
                    torrents.banned, torrents.leechers, torrents.info_hash, 
                    torrents.filename, torrents.nfo, torrents.last_action, 
                    torrents.numratings, torrents.name, torrents.tmdb, 
                    torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, 
                    torrents.visible, torrents.size, torrents.added, torrents.views, 
                    torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.torrentlang,
                    torrents.external, torrents.image1, torrents.image2, torrents.announce, 
                    torrents.numfiles, torrents.freeleech, torrents.vip, torrents.category, torrents.sticky,
                    torrents.announcelist, torrents.filelist,
                    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, 
                    torrents.numratings,
                    categories.id AS cat_id,
                    categories.name AS cat_name, 
                    torrentlang.name AS lang_name, 
                    torrentlang.image AS lang_image, 
                    categories.parent_cat as cat_parent, 
                    users.username, users.privacy 
            FROM torrents 
            LEFT JOIN categories ON torrents.category = categories.id 
            LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
            LEFT JOIN users ON torrents.owner = users.id 
            WHERE $where",
            $value)->fetch();
        if (!$stmt || ($stmt["banned"] == "yes")) {
            showMessage(Url(), LANG["TORRENT_NOT_FOUND"]);
        }
        // Torrent
        $torrent = $stmt;
        // Name
        $name = $stmt["name"];
        $views = $stmt['views'];
        // TMDB
        $tmdb1 = MDBS::createMDB($stmt['id'], $stmt);

        // Image
        if ($stmt["imdb"] != "") {
            $image = "<img src=" . $tmdb1['POSTER'] . " class='scaleA'><br>";
        } elseif ($stmt["tmdb"] != "") {
            $image = "<img src='" . data_uri($tmdb1['POSTER'], $tmdb1['POSTER']) . "' height='450' width='100%' />";
        } elseif ($stmt["image1"] != "") {
            $image = "<img src='" . data_uri(CONFIG['UPLOADDIR'] . "/images/" . $stmt["image1"], $stmt['image1']) . "' height='250' width='100%' />";
        } else {
            $image = "";
        }
        unset($tmdb1['POSTER']);
        // Video
        if (array_key_exists('video', $tmdb1)) {
            $video = $tmdb1['video'];
            unset($tmdb1['video']);
        }
        // Thanks/Likes
        $thanks = (new LikesModel)->showThanks($stmt['id']);
        $likes = (new LikesModel)->showLikes($stmt);
        // Tags
        $tag = (new TorrentTagModel)->fetchRow('type1, type2, type3', ['torrentid='], [$value]);
        if ($tag) {
            $tags = get_tags($value);
        } else {
            $tags = '---';
        }
        // Html
        $htl['shortname'] = CutName(htmlspecialchars($stmt["name"]), 40);
        $html['taglist'] = (new TagsModel)->getAll();
        $html['catdrop'] = CatagoriesModel::dropdown($stmt['cat_id']);
        $html['torrentlang'] == null ? 'unknown' : $stmt['torrentlang'];
        $html['ratings'] = (new RatingsModel)->ratingTor($stmt['id'], $data->id, $stmt['rating']);
        $html['externaltorrent'] = $stmt["external"] == 'yes' ? "<a type='#' class='model_form ttbtn btn-sm' data-bs-toggle='modal' data-bs-target='#trackerModal'>". LANG["TRACKERS"] ."</a>" : LANG['LOCAL'];
        $html['nfo'] = $stmt["nfo"] == "yes" ? "<a href='#' data-bs-toggle='modal' data-bs-target='#nfoModal'><i class='fa fa-file fa-1x' title='". LANG["NFO"] ."'></i></a>" : LANG['NOTHING_FOUND'];
        $html['langname'] = empty($stmt["lang_name"]) ? "Unknown/NA" : "<img src='" . Url("assets/images/languages/$stmt[lang_image]") . "' alt='" . $stmt["lang_name"] . "' width='20' height='20'/>";
        $html['lang'] = TorrentLangModel::langList($stmt['torrentlang']);
        $html['bookmark'] = (new BookmarksModel)->select($stmt['id'], $data);
        $html['vip1'] = ($stmt["vip"] == "yes") ? 1 : 0;
        $html['freeleech1'] = $stmt["freeleech"] == 1 ? "<font color=green><b>Yes</b></font>" : "<font color=red><b>No</b></font>";
        // Calculate local torrent speed test
        if ($stmt["leechers"] >= 1 && $stmt["seeders"] >= 1 && $stmt["external"] != 'yes') {
            $speedQ = DB::run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$stmt[id]' GROUP BY t.id ORDER BY added ASC LIMIT 15");
            $a = $speedQ->fetch(PDO::FETCH_ASSOC);
            $html['totalspeed'] = mksize($a["totalspeed"]) . "/s";
        } else {
            $html['totalspeed'] = LANG["NO_ACTIVITY"];
        }

        // Scraper
        $ts = modify('date', $stmt['last_action'], '+2 day');
        $scraper = $ts > CONFIG['TT_DATE'] ? 
                    "<b>Stats Recently Updated</b>" : 
                    "<form action='" . CONFIG['URLROOT'] . "/scrape/external?id=" . $stmt['id'] . "' method='post'>
                    <button type='submit' class='btn ttbtn center-block' value=''>" . LANG["UPDATE_STATS"] . "</button>
                    </form>";

        $data = [
            'torrent' => $torrent,
            'name' => $name,
            'views' => $views,
            'tmdb1' => $tmdb1,
            'image' => $image,
            'video' => $video,
            'likes' => $likes,
            'thanks' => $thanks,
            'tags' => $tags,
            'html' => $html,
            'scraper' => $scraper
        ];
        
        return $data;
    }

    // Update Comments
    public function updateComments($id, $var)
    {
        if ($var == 'sub') {
            $row = DB::fetchRow('torrents', 'comments', ['id='], [$id]);
            DB::run("UPDATE torrents SET comments = $row[comments] - 1 WHERE id = $id");
        } elseif ($var == 'add') {
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
    }

    // Function To Delete A Torrent
    public function deleteTorrent($id, $current_userid)
    {
        $row = DB::fetchRow('torrents', 'image1,image2', ['id'], [$id]);
        foreach (explode(".", "peers.comments.ratings.files") as $x) {
            DB::delete($x, ['torrent' => $id]);
        }
        DB::delete('completed', ['torrentid' => $id]);
        if (file_exists(CONFIG['UPLOADDIR'] . "/torrents/$id.torrent")) {
            unlink(CONFIG['UPLOADDIR'] . "/torrents$id.torrent");
        }
        if ($row["image1"]) {
            unlink(CONFIG['UPLOADDIR'] . "/images/" . $row["image1"]);
        }
        if ($row["image2"]) {
            unlink(CONFIG['UPLOADDIR'] . "/images/" . $row["image2"]);
        }
        @unlink(CONFIG['UPLOADDIR'] . "/nfos/$id.nfo");
        DB::delete('torrents', ['id' => $id]);
        DB::delete('files', ['torrent' => $id]);
        (new ReportsModel)->delete(['votedfor' => $id, 'type' => 'torrent']);
        DB::delete('snatched', ['tid' => $id]);
        DB::delete('bookmarks', ['targetid' => $id, 'type' => 'torrent']);
        DB::delete('likes', ['liked' => $id, 'type' => 'torrent']);
        
        (new LogsModel)->write("Torrent ID $id was deleted by ".$current_userid."");
    }

    // search torrent
    public function countSearch()
    {
        // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents " . $search['where'], $search['params'])->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'] . "/search?$search[url]$search[pagerlink]");

        $torrents = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.tube,  torrents.tmdb, torrents.imdb, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed,
        torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name
        AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy,
        IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1))
        AS rating FROM torrents
        LEFT JOIN categories
        ON category = categories.id
        LEFT JOIN users
        ON torrents.owner = users.id
        $search[where] $search[orderby] $limit", $search['params']);
        $cats = CatagoriesModel::genreList();
        $lang = TorrentLangModel::langList();
        $title = $search['keyword'] == '' ? LANG["SEARCH"] :LANG["SEARCH_RESULTS_FOR"] . " \"" . htmlspecialchars($search['keyword']) . "\"";
        
        return  array('title' => $title, 'torrents' => $torrents, 'pager' => $pager, 'search' => $search, 'cats' => $cats, 'lang' => $lang);
    }

    // Scrape Info
    public function scrapeInfo($hashes)
    {
        $cres = DB::run("SELECT info_hash, seeders, leechers, times_completed, filename FROM `torrents` WHERE `info_hash` IN (?)", [$hashes])->fetch(PDO::FETCH_ASSOC);
        return $cres;
    }

    // RSS Info
    public function rssInfo($where, $limit)
    {
        $cres = DB::run("SELECT torrents.id,
                               torrents.name,
                               torrents.size,
                               torrents.category,
                               torrents.added, torrents.leechers,
                               torrents.seeders, categories.parent_cat as cat_parent,
                               categories.name AS cat_name 
                        FROM torrents 
                        LEFT JOIN categories ON category = categories.id 
                        $where 
                        ORDER BY added 
                        DESC $limit")->fetchAll();
        return $cres;
    }

    // Browse Page
    public function countBrowse()
    { 
       // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $search[where]", $search['params'])->fetchColumn();
        
        $catsquery = (new CatagoriesModel)->getCatByParent();

        if ($count) {
            // Pagination
            list($pagerbuttons, $limit) = pagination(25, $count, CONFIG['URLROOT'] . "/browse" . $search['url'] . $search['pagerlink']);
            $res = DB::run("SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, 
            torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, 
            torrents.tube, torrents.tmdb, torrents.imdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, 
            torrents.filename, torrents.owner, torrents.external, torrents.freeleech, 
            categories.name AS cat_name, categories.parent_cat AS cat_parent, 
            categories.image AS cat_pic, users.username, users.privacy, 
            IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating 
            FROM torrents 
            LEFT JOIN categories 
            ON category = categories.id 
            LEFT JOIN users 
            ON torrents.owner = users.id $search[where] $search[orderby] $limit",
            $search['params']);

        } else {
            unset($res);
            $pagerbuttons = $res = '';
        }

        // Get Cat Data
        $cats = (new CatagoriesModel)->fetchAll('*', '', 'ORDER BY parent_cat, name');

        return array('res' => $res, 'pagerbuttons' => $pagerbuttons, 'catsquery' => $catsquery, 'count' => $count, 'search' => $search, 'cats' => $cats,);
    }

    public function reseed($id, $current_user)
    {
        if (isset($_COOKIE["reseed$id"])) {
            showMessage(Url(), LANG["RESEED_ALREADY_ASK"]);
        }

        // Check Torrent
        $row = (new TorrentsModel)->fetchRow('owner,banned,external', ['id ='], [$id]);
        if (!$row || $row["banned"] == "yes") {
            showMessage(Url(), LANG["TORRENT_NOT_FOUND"]);
        } elseif ($row["external"] == "yes") {
            showMessage(Url(), LANG["EXTERNAL"]);
        }

        // Send Message
        $res2 = (new SnatchedModel)->completed($id);
        $message = sprintf(LANG['RESEED_MESSAGE'], $current_user->username, CONFIG['URLROOT'], $id);
        foreach ($res2 as $row2) {
            MessagesModel::insert(['subject' => LANG["NEW_RESEED"], 'sender' => $current_user->id, 'receiver' => $row2['id'], 'added' => get_date_time(), 'msg' => $message]);
        }
        if ($row["owner"] && $row["owner"] != $current_user->id) {
            MessagesModel::insert(['subject' => 'Torrent Reseed Request', 'sender' => $current_user->id, 'receiver' => $row['owner'], 'added' => get_date_time(), 'msg' => $message]);
        }

        setcookie("reseed$id", $id, time()+60*60*24*30);
        showMessage(Url(), LANG["RESEED_SENT"]);
    }
    
    // Search Torrent  todo blocks&views below
    public static function popular($tcat)
    {
        $row = DB::run("SELECT torrents.id, torrents.name, torrents.image1, torrents.image2, torrents.poster, torrents.tmdb, torrents.imdb, categories.name as cat_name, categories.parent_cat as cat_parent FROM torrents LEFT JOIN categories ON torrents.category=categories.id 
		WHERE banned = 'no' AND visible = 'yes' AND category IN ($tcat) ORDER BY torrents.seeders + torrents.leechers DESC LIMIT 10")->fetchAll();
        return $row;
    }

    // Get All User Array
    public static function getAll($where = false, $values = '', $pagination = false)
    {
        if (!$where) {
            $where = "WHERE torrents.visible = ?";
            $values = array('yes');
        }

        $count = DB::run("SELECT COUNT(*) FROM torrents $where", $values)->fetchColumn();
        if ($pagination) {
            list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'] . "/$pagination&amp;");
        } else {
            $limit = 'LIMIT 20';
            $pager = '';
        }
        $stmt = DB::run(
            "SELECT torrents.anon, torrents.seeders, torrents.tube, 
                    torrents.banned, torrents.leechers, torrents.info_hash, 
                    torrents.filename, torrents.nfo, torrents.last_action, 
                    torrents.numratings, torrents.name, torrents.tmdb,  torrents.comments,
                    torrents.imdb, torrents.owner, torrents.save_as, torrents.descr, 
                    torrents.visible, torrents.size, torrents.added, torrents.views, 
                    torrents.hits, torrents.times_completed, torrents.id, torrents.type, 
                    torrents.external, torrents.image1, torrents.image2, torrents.poster, torrents.announce, 
                    torrents.numfiles, torrents.freeleech, torrents.vip, torrents.category, torrents.sticky,
                    torrents.announcelist, torrents.filelist,
                    IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, 
                    torrents.numratings, 
                    categories.name AS cat_name, 
                    categories.image AS cat_pic,
                    torrentlang.name AS lang_name, 
                    torrentlang.image AS lang_image, 
                    categories.parent_cat as cat_parent, 
                    users.username, users.privacy 
            FROM torrents 
            LEFT JOIN categories ON torrents.category = categories.id 
            LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id 
            LEFT JOIN users ON torrents.owner = users.id
             $where $limit",
            $values
        )->fetchAll(PDO::FETCH_ASSOC);

        $array = [
            'stmt' => $stmt,
            'pager' => $pager,
            'count' => $count
        ];

        return $array;
    }

    // Get Cat
    public static function getCatSortAll($where, $date_time, $orderby, $limit)
    {
        $row = DB::run("SELECT torrents.id, torrents.anon, torrents.category, torrents.sticky, torrents.tmdb, torrents.imdb, torrents.tube, torrents.tube, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size,
                               torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name
                        AS cat_name, categories.parent_cat
                        AS cat_parent, categories.image
                        AS cat_pic, users.username, users.privacy
                        FROM torrents
                        LEFT JOIN categories
                        ON category = categories.id
                        LEFT JOIN users
                        ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby $limit");
        return $row;
    }
}