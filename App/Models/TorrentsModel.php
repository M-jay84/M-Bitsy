<?php

namespace App\Models;

use App\Libs\DB;
use PDO;
use App\Libs\Torrents\MDBS;
use App\Models\LikesModel;
use App\Models\TorrentTagModel;
use App\Models\TagsModel;
use App\Models\CatagoriesModel;
use App\Models\RatingsModel;
use App\Models\ReportsModel;
use App\Models\TorrentLangModel;
use App\Models\LogsModel;
use App\Models\SnatchedModel;
use App\Models\MessagesModel;

// Torrent Search
function search()
{
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $cats = (int) isset($_GET['cat']) ? $_GET['cat'] : 0;
    $incldead = (int) isset($_GET['incldead']) ? $_GET['incldead'] : 0;
    $freeleech = (int) isset($_GET['freeleech']) ? $_GET['freeleech'] : 0;
    $inclexternal = (int) isset($_GET['inclexternal']) ? $_GET['inclexternal'] : 0;
    $lang = (int) isset($_GET['language']) ? $_GET['language'] : 0;
    
    $url = "?"; // assign url
    $wherea = []; // assign conditions
    $params = []; // assign vars
    // browse
    $cats = (int) (isset($_GET['cat'])) ? $_GET['cat'] : 0;
    $parent_cat = (isset($_GET['parent_cat'])) ? $_GET['parent_cat'] : 0;

    if (!$keyword == '') {
        $keys = explode(" ", $keyword);
        foreach ($keys as $k) {
            $ssa[] = " torrents.name LIKE '%$k%' ";
        }
        $wherea[] = '(' . implode(' OR ', $ssa) . ')';
        $url .= "keyword=" . urlencode($keyword) . "&";
    }

    if (!$cats == 0) {
        $wherea[] = "category = $cats";
        $url .= "cat=" . urlencode($cats) . "&";
    }

    if (!$parent_cat == 0) {
        $wherea[] = "categories.parent_cat = $cats";
        $url .= "parent_cat=" . urlencode($cats) . "&";
    }

    if ($incldead == 1) {
        $url .= "incldead=1&";
    } elseif ($incldead == 2) {
        $params[] = 'no';
        $wherea[] = "visible = ?";
        $url .= "incldead=2&";
    } else {
        $params[] = 'yes';
        $wherea[] = "visible = ?";
    }

    if ($freeleech == 1) {
        $params[] = 0;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=1&";
    } elseif ($freeleech == 2) {
        $params[] = 1;
        $wherea[] = "freeleech = ?";
        $url .= "freeleech=2&";
    }

    if ($inclexternal == 1) {
        $params[] = 'no';
        $wherea[] = "external = ?";
        $url .= "inclexternal=1&";
    } elseif ($inclexternal == 2) {
        $params[] = 'yes';
        $wherea[] = "external = ?";
        $url .= "inclexternal=2&";
    }

    if ($lang) {
        $params[] = $lang;
        $wherea[] = "torrentlang = ?";
        $url .= "lang=" . urlencode($lang) . "&";
    }

    $where = implode(' AND ', $wherea);

    // browse
    $wherecatina = array();
    $wherecatin = "";
    $res = DB::fetchAll('categories', 'id', '');
    foreach ($res as $row) {
        if (isset($_GET["c$row[id]"])) {
            $wherecatina[] = $row["id"];
            $url .= "c$row[id]=1&";
        }
        $wherecatin = implode(", ", $wherecatina);
    }

    if ($wherecatin) {
        $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
    }

    if ($where != '') {
        $where = 'WHERE ' . $where;
    }
    $sort = $_GET['sort'] ?? '';
    $order = $_GET['order'] ?? '';
    switch ($sort) {
        case 'id':$column = "id";
            break;
        case 'name':$column = "name";
            break;
        case 'comments':$column = "comments";
            break;
        case 'size':$column = "size";
            break;
        case 'times_completed':$column = "times_completed";
            break;
        case 'seeders':$column = "seeders";
            break;
        case 'leechers':$column = "leechers";
            break;
        case 'category':$column = "category";
            break;
        default:$column = "id";
            break;
    }

    switch ($order) {
        case 'asc':$ascdesc = "ASC";
            break;
        case 'desc':$ascdesc = "DESC";
            break;
        default:$ascdesc = "DESC";
            break;
    }

    $orderby = 'ORDER BY torrents.' . $column . ' ' . $ascdesc;
    $pagerlink = "sort=" . $column . "&amp;order=" . strtolower($ascdesc) . "&amp;";

    $sortmod = [
        'orderby' => $orderby, 'pagerlink' => $pagerlink,
        'column' => $column, 'by' => $ascdesc
    ];
    $orderby = 'ORDER BY torrents.' . $sortmod['column'] . ' ' . $sortmod['by'];
    $pagerlink = $sortmod['pagerlink'];

    $array = [
        'where'=>$where,
        'keyword'=>$keyword,
        'orderby'=>$orderby,
        'params'=>$params,
        'url'=>$url,
        'pagerlink'=>$pagerlink,
        'parent_cat' => $parent_cat,
        'wherecatina' => $wherecatina,
    ];
    return $array;
}

/*
* Torrent Table Data
*/
class TorrentsModel
{
    // Fetch Torrent Details
    public static function updateset($updateset, $id)
    {
		$stmt = DB::updateset("torrents", $updateset, ['id' => $id]);
        return $stmt;
	}

    // Fetch Torrent Details
    public static function insert($updateset)
    {
        $stmt = DB::insert('torrents', $updateset);
        return $stmt;
	}
	
    // Fetch Torrent Details
    public static function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('torrents', $cols, $where, $vals, $other);
        return $stmt;
	}
	
    // Fetch Torrent Details
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('torrents', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
	
    // Fetch Torrent Details
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('torrents', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Torrent Details
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('torrents', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch Torrent Details
    public static function fetchIdDetails($where, $value, $data)
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
            showMessage(URLROOT, LANG["TORRENT_NOT_FOUND"]);
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
            $image = "<img src='" . data_uri(UPLOAD_PATH . "/images/" . $stmt["image1"], $stmt['image1']) . "' height='250' width='100%' />";
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
        $thanks = LikesModel::showThanks($stmt['id']);
        $likes = LikesModel::showLikes($stmt);
        // Tags
        $tag = TorrentTagModel::fetchRow('type1, type2, type3', ['torrentid='], [$value]);
        if ($tag) {
            $tags = get_tags($value);
        } else {
            $tags = '---';
        }
        // Html
        $htl['shortname'] = CutName(htmlspecialchars($stmt["name"]), 40);
        $html['taglist'] = TagsModel::getAll();
        $html['catdrop'] = CatagoriesModel::dropdown($stmt['cat_id']);
        $html['torrentlang'] == null ? 'unknown' : $stmt['torrentlang'];
        $html['ratings'] = RatingsModel::ratingTor($stmt['id'], $data->id, $stmt['rating']);
        $html['externaltorrent'] = $stmt["external"] == 'yes' ? "<a type='#' class='model_form ttbtn btn-sm' data-bs-toggle='modal' data-bs-target='#trackerModal'>". LANG["TRACKERS"] ."</a>" : LANG['LOCAL'];
        $html['nfo'] = $stmt["nfo"] == "yes" ? "<a href='#' data-bs-toggle='modal' data-bs-target='#nfoModal'><i class='fa fa-file fa-1x' title='". LANG["NFO"] ."'></i></a>" : LANG['NOTHING_FOUND'];
        $html['langname'] = empty($stmt["lang_name"]) ? "Unknown/NA" : "<img src='" . Url("assets/images/languages/$stmt[lang_image]") . "' alt='" . $stmt["lang_name"] . "' width='20' height='20'/>";
        $html['lang'] = TorrentLangModel::langList($stmt['torrentlang']);
        $html['vip1'] = ($stmt["vip"] == "yes") ? 1 : 0;
        $html['freeleech1'] = $stmt["freeleech"] == 1 ? "<font color=green><b>Yes</b></font>" : "<font color=red><b>No</b></font>";
        // Calculate local torrent speed test
        if ($stmt["leechers"] >= 1 && $stmt["seeders"] >= 1 && $stmt["external"] != 'yes') {
            $speedQ = DB::run("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('" . CONFIG['TT_DATE'] . "') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$stmt[id]' GROUP BY t.id ORDER BY added ASC LIMIT 15");
            $a = $speedQ->fetch(PDO::FETCH_ASSOC);
            $html['totalspeed'] = mksize($a["totalspeed"]) . "/s";
        } else {
            $html['totalspeed'] = LANG["NO_ACTIVITY"];
        }

        // Scraper
        $ts = modify('date', $stmt['last_action'], '+2 day');
        $scraper = $ts > CONFIG['TT_DATE'] ? 
                    "<b>Stats Recently Updated</b>" : 
                    "<form action='" . URLROOT . "/scrape/external?id=" . $stmt['id'] . "' method='post'>
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
    public static function updateComments($id, $var)
    {
        if ($var == 'sub') {
            $row = DB::fetchRow('torrents', 'comments', ['id='], [$id]);
            DB::run("UPDATE torrents SET comments = $row[comments] - 1 WHERE id = $id");
        } elseif ($var == 'add') {
            DB::run("UPDATE torrents SET comments = comments + 1 WHERE id = $id");
        }
    }

    // Function To Delete A Torrent
    public static function deleteTorrent($id, $current_userid)
    {
        $row = DB::fetchRow('torrents', 'image1,image2', ['id'], [$id]);
        foreach (explode(".", "peers.comments.ratings.files") as $x) {
            DB::delete($x, ['torrent' => $id]);
        }
        DB::delete('completed', ['torrentid' => $id]);
        if (file_exists(UPLOAD_PATH . "/torrents/$id.torrent")) {
            unlink(UPLOAD_PATH . "/torrents$id.torrent");
        }
        if ($row["image1"]) {
            unlink(UPLOAD_PATH . "/images/" . $row["image1"]);
        }
        if ($row["image2"]) {
            unlink(UPLOAD_PATH . "/images/" . $row["image2"]);
        }
        @unlink(UPLOAD_PATH . "/torrents/nfos/$id.nfo");
        DB::delete('torrents', ['id' => $id]);
        DB::delete('files', ['torrent' => $id]);
        ReportsModel::delete(['votedfor' => $id, 'type' => 'torrent']);
        DB::delete('snatched', ['tid' => $id]);
        DB::delete('likes', ['liked' => $id, 'type' => 'torrent']);
        
        LogsModel::write("Torrent ID $id was deleted by ".$current_userid."");
    }

    // search torrent
    public static function countSearch()
    {
        // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents " . $search['where'], $search['params'])->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, URLROOT . "/search?$search[url]$search[pagerlink]");

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
    public static function scrapeInfo($hashes)
    {
        $cres = DB::run("SELECT info_hash, seeders, leechers, times_completed, filename FROM `torrents` WHERE `info_hash` IN (?)", [$hashes])->fetch(PDO::FETCH_ASSOC);
        return $cres;
    }

    // RSS Info
    public static function rssInfo($where, $limit)
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
    public static function countBrowse()
    { 
       // Search Torrent ($_GET)
        $search = search();

        $count = DB::run("SELECT COUNT(*) FROM torrents LEFT JOIN categories ON category = categories.id $search[where]", $search['params'])->fetchColumn();
        
        $catsquery = CatagoriesModel::getCatByParent();

        if ($count) {
            // Pagination
            list($pagerbuttons, $limit) = pagination(25, $count, URLROOT . "/browse" . $search['url'] . $search['pagerlink']);
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
        $cats = CatagoriesModel::fetchAll('*', '', 'ORDER BY parent_cat, name');

        return array('res' => $res, 'pagerbuttons' => $pagerbuttons, 'catsquery' => $catsquery, 'count' => $count, 'search' => $search, 'cats' => $cats,);
    }

    public static function reseed($id, $current_user)
    {
        if (isset($_COOKIE["reseed$id"])) {
            showMessage(URLROOT, LANG["RESEED_ALREADY_ASK"]);
        }

        // Check Torrent
        $row = TorrentsModel::fetchRow('owner,banned,external', ['id ='], [$id]);
        if (!$row || $row["banned"] == "yes") {
            showMessage(URLROOT, LANG["TORRENT_NOT_FOUND"]);
        } elseif ($row["external"] == "yes") {
            showMessage(URLROOT, LANG["EXTERNAL"]);
        }

        // Send Message
        $res2 = SnatchedModel::completed($id);
        $message = sprintf(LANG['RESEED_MESSAGE'], $current_user->username, URLROOT, $id);
        foreach ($res2 as $row2) {
            MessagesModel::insert(['subject' => LANG["NEW_RESEED"], 'sender' => $current_user->id, 'receiver' => $row2['id'], 'added' => CONFIG['TT_DATE'], 'msg' => $message]);
        }
        if ($row["owner"] && $row["owner"] != $current_user->id) {
            MessagesModel::insert(['subject' => 'Torrent Reseed Request', 'sender' => $current_user->id, 'receiver' => $row['owner'], 'added' => CONFIG['TT_DATE'], 'msg' => $message]);
        }

        setcookie("reseed$id", $id, CONFIG['TT_TIME']+60*60*24*30);
        showMessage(URLROOT, LANG["RESEED_SENT"]);
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
            list($pager, $limit) = pagination(25, $count, URLROOT . "/$pagination&amp;");
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