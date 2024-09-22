<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\SimpleThumbnail;
use App\Libs\Validate;
use App\Libs\Torrents\Parse;
use App\Models\LogsLogic;
use App\Models\ShoutboxLogic;
use App\Models\TorrentsLogic;
use App\Models\TorrentTagLogic;

/*
* Users Table Data
*/
class UploadLogic
{
    public $current_user;
    
    // Fetch Torrent Details
    public static function torrent($current_user)
    {
     // Check Form Data.
     if (!isset($_POST['type'], $_POST['name'], $_FILES['torrent'])) {
        $message = LANG['MISSING_FORM_DATA'];
    }
    // Upload File
    if (($num = $_FILES['torrent']['error'])) {
        showMessage('upload', LANG["UPLOAD_ERR[$num]"]);
    }
    // Get Name
    if (!($fname = $_FILES["torrent"]["name"])) {
        $message = LANG["EMPTY_FILENAME"];
    }
    // Check File Name
    if (!preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $fname)) {
        $message = LANG["UPLOAD_INVALID_FILENAME"];
    }
    // Check File Torrent
    if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches)) {
        $message = LANG["NOT_TORRENT"];
    }
    // Check NFO
    $nfo = 'no';
    if ($_FILES['nfo']['size'] != 0) {
        $nfofile = $_FILES['nfo'];
        if ($nfofile['name'] == '') {
            $message = LANG["NO_NFO_UPLOADED"];
        }
        if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches)) {
            $message = LANG["UPLOAD_NOT_NFO"];
        }
        if ($nfofile['size'] == 0) {
            $message = LANG["NO_NFO_SIZE"];
        }
        if ($nfofile['size'] > 65535) {
            $message = LANG["NFO_UPLOAD_SIZE"];
        }
        $nfofilename = $nfofile['tmp_name'];
        if (($num = $_FILES['nfo']['error'])) {
            $message = LANG["UPLOAD_ERR[$num]"];
        }
        $nfo = 'yes';
    }
    // Check User Inputs
    $descr = Validate::Input('descr') ?? LANG["UPLOAD_NO_DESC"];
    $vip = Validate::Input('vip', 'int');
    $free = Validate::Input('free', 'int');

    $tube = Validate::Input('tube');
    $tmdb = Validate::Input('tmdb');
    $imdb = Validate::Input('imdb');
    $anon = (isset($_POST['anonycheck'])) ? 'yes' : 'no';
    $langid = Validate::Input('language', 'int');
    $catid = Validate::Input('type', 'int');
    // Check Cat Selected
    if (!intval($catid)) {
        $message = LANG["UPLOAD_NO_CAT"];
    }
    // Shorten Name
    if (!empty($_POST['name'])) {
        $name = Validate::Input('name');
    }
    // If Message Show
    if ($message) {
        showMessage('upload', $message);
    }

    // No Message So Continue
    if (!$message) {
        // Torrent / NFO Dir
        $torrent_dir = UPLOAD_PATH."/torrents";
        $nfo_dir = UPLOAD_PATH."/nfos";

        // Move Torrent File
        if (!move_uploaded_file($_FILES["torrent"]["tmp_name"], "$torrent_dir/$fname")) {
            showMessage('upload', LANG["ERROR"] . ": " . LANG["UPLOAD_COULD_NOT_BE_COPIED"] . " $torrent_dir - $fname");
        }

        // Parse Torrent File
        $tor = Parse::torr("$torrent_dir/$fname");

        // Check External
        $announce_urls = explode(",", URLROOT . CONFIG['ANNOUNCELIST']);
        if (!in_array($tor['announce'], $announce_urls)){
            $external='yes';
        }else{
            $external='no';
        }
        if (!CONFIG['ALLOWEXTERNAL'] && $external == 'yes') {
            $message = LANG["UPLOAD_NO_TRACKER_ANNOUNCE"];
        }
        if ($external === 'yes') {
            $announcelist = serialize($tor["announce-list"]);
        } else {
            $announcelist = serialize([$tor["announce"]]);
        }

    }

    // Error So Delete File
    if ($message) {
        unlink("$torrent_dir/$fname");
        showMessage('upload', $message);
    }

    // Check Name
    if ($name == "") {
        $name = $tor['name'];
    }
    $name = str_replace(".torrent", "", $name);
    $name = str_replace("_", " ", $name);

    // Upload Images
    $allowed_types = CONFIG['ALLOWEDIMAGETYPES'];
    $inames = array();
    for ($x = 0; $x < 2; $x++) {
        if (!($_FILES['image' . $x]['name'] == "")) {
            $y = $x + 1;
            if ($_FILES['image$x']['size'] > CONFIG['IMAGEMAXFILESIZE']) {
                showMessage('upload', LANG["INVAILD_FILE_SIZE_IMAGE"]);
            }
            $uploaddir = UPLOAD_PATH . '/images/';
            $ifile = $_FILES['image' . $x]['tmp_name'];
            $im = getimagesize($ifile);
            if (!$im[2]) {
                showMessage('upload', sprintf(LANG["INVALID_IMAGE"]));
            }
            if (!array_key_exists($im['mime'], $allowed_types)) {
                showMessage('upload', LANG["INVALID_FILETYPE_IMAGE"]);
            }
            $row = DB::run("SHOW TABLE STATUS LIKE 'torrents'")->fetch();
            $next_id = $row['Auto_increment'];
            $ifilename = $next_id . $x . $allowed_types[$im['mime']];
            SimpleThumbnail::create()->copyImage($ifile, $uploaddir . $ifilename);
            $inames[] = $next_id . $x . '.webp';
        }
    }

    // Insert Torrent
    try {
        TorrentsLogic::insert([
            'filename'=>$fname, 'owner'=>$current_user->id, 'name'=>$name, 'vip'=>$vip, 'descr'=>$descr, 'image1'=>$inames[0], 'image2'=>$inames[1], 'category'=>$catid, 
            'tube'=>$tube, 'added'=>CONFIG['TT_DATE'], 'info_hash'=>$tor['hash'], 'size'=>$tor['length'], 'numfiles'=>(int) $tor['ttfilecount'], 'save_as'=>$fname,
            'announce'=>$tor['announce'], 'external'=>$external, 'nfo'=>$nfo, 'torrentlang'=>$langid, 'anon'=>$anon, 'last_action'=>CONFIG['TT_DATE'], 'freeleech'=>$free,
            'tmdb'=>$tmdb, 'imdb'=>$imdb, 'announcelist'=>$announcelist, 'filelist'=>$tor['files'], 'info_hash2'=>$tor['hash2']
        ]);
    } catch (\PDOException $e) {
        TorrentsLogic::deleteTorrent(DB::lastInsertId(), $current_user->id);
        rename("$torrent_dir/$fname", "$torrent_dir/duplicate.torrent"); // todo
        showMessage('upload', LANG['ACTION_FAILED']);
    }

    $id = DB::lastInsertId();

    // Rename To INT
    rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent");

    // Tags Mod
    $tags = $_POST['tags'];
    if ($tags) {
        TorrentTagLogic::insert(['torrentid' => $id, 'type1' => $tags[0], 'type2' => $tags[1], 'type3' => $tags[2]]);
    }

    // Move NFO
    if ($nfo == 'yes') {
        move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
    }

    // Log Upload
    LogsLogic::write(sprintf(LANG["TORRENT_UPLOADED"], htmlspecialchars($name), $current_user->username));
    
    // Shout Torrent
    $msg_shout = "New Torrent: [url=" . URLROOT . "/torrent/" . $id . "]" . $name . "[/url] has been uploaded " . ($anon == 'no' ? "by [color=".$current_user->Color."]" . $current_user->username . "[/color]" : "") . "";
    ShoutboxLogic::insert(['userid'=>0, 'date'=>CONFIG['TT_DATE'], 'user'=>'System', 'message'=>$msg_shout]);

    //Uploaded ok message
    if ($external == 'no') {
        $message = sprintf(LANG["TORRENT_UPLOAD_LOCAL"], $name, $id, $id);
    } else {
        $message = sprintf(LANG["TORRENT_UPLOAD_EXTERNAL"], $name, $id);
        // Scrape External
        Parse::ScrapeId($id, $tor["announce-list"], $tor['hash']);
    }

    showMessage("torrent/$id", $message);
	}
}