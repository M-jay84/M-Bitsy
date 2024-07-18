<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Libs\DB;
use App\Libs\Parse;
use App\Libs\Tscraper;
use App\Models\CatagoriesModel;
use App\Models\TorrentsModel;
use App\Models\LogsModel;
use App\Models\TagsModel;
use App\Models\ShoutboxModel;
use App\Models\TorrentLangModel;
use App\Models\TorrentTagModel;

/*
* Upload Controller
*/
class Upload 
{
    // Upload Default Page
    public function index()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('can_upload')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (CONFIG['UPLOADERSONLY'] && $USER->has('class') < _VIP) {
            Redirect(Url(), Txt("UPLOAD_ONLY_FOR_UPLOADERS"));
        }
        // Get Announce List
        $announce_urls = explode(",", ANNOUNCELIST);
        $cats = CatagoriesModel::genreList();
        $tags = TagsModel::getAll();
        $languages = TorrentLangModel::langList();

        // Init Data
        $data = [
            'V_title' => Txt("UPLOAD"),
            'V_announce_urls' => $announce_urls,
            'V_cats' => $cats,
            'V_tags' => $tags,
            'V_languages' => $languages,
            'USER' => $USER
        ];
        
        // Load View
        template('upload', $data, 'user');
    }

    // Upload Form Submit
    public function submit()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('can_upload')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        if (CONFIG['UPLOADERSONLY'] && $USER->has('class') < _VIP) {
            Redirect(Url(), Txt("UPLOAD_ONLY_FOR_UPLOADERS"));
        }
        if (!empty($_POST)) {
            // Check Form Data.
            if (!isset($_POST['type'], $_POST['name'], $_FILES['torrent'])) {
                $message = Txt('MISSING_FORM_DATA');
            }
            // Upload File
            if (($num = $_FILES['torrent']['error'])) {
                Redirect(Url('upload'), Txt("UPLOAD_ERR[$num]"));
            }
            // Get Name
            if (!($fname = $_FILES["torrent"]["name"])) {
                $message = Txt("EMPTY_FILENAME");
            }
            // Check File Name
            if (!preg_match('/^[^\0-\x1f:\\\\\/?*\xff#<>|]+$/si', $fname)) {
                $message = Txt("UPLOAD_INVALID_FILENAME");
            }
            // Check File Torrent
            if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches)) {
                $message = Txt("NOT_TORRENT");
            }
            // Check NFO
            $nfo = 'no';
            if ($_FILES['nfo']['size'] != 0) {
                $nfofile = $_FILES['nfo'];
                if ($nfofile['name'] == '') {
                    $message = Txt("NO_NFO_UPLOADED");
                }
                if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches)) {
                    $message = Txt("UPLOAD_NOT_NFO");
                }
                if ($nfofile['size'] == 0) {
                    $message = Txt("NO_NFO_SIZE");
                }
                if ($nfofile['size'] > 65535) {
                    $message = Txt("NFO_UPLOAD_SIZE");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (($num = $_FILES['nfo']['error'])) {
                    $message = Txt("UPLOAD_ERR[$num]");
                }
                $nfo = 'yes';
            }
            // Check User Inputs
            $descr = Input('desscr') ?? Txt("UPLOAD_NO_DESC");
            $vip = Input('vip', 'int');
            $free = Input('free', 'int');

            $tube = Input('tube');
            $tmdb = Input('tmdb');
            $imdb = Input('imdb');
            $anon = (isset($_POST['anonycheck'])) ? 'yes' : 'no';
            $langid = Input('language', 'int');
            $catid = Input('type', 'int');
            // Check Cat Selected
            if (!Id($catid)) {
                $message = Txt("UPLOAD_NO_CAT");
            }
            // Shorten Name
            if (!empty($_POST['name'])) {
                $name = Input('name');
            }
            // If Message Show
            if ($message) {
                Redirect(Url('upload'), $message);
            }

            // No Message So Continue
            if (!$message) {
                // Torrent / NFO Dir
                $torrent_dir = UPLOADDIR."/torrents";
                $nfo_dir = UPLOADDIR."/nfos";

                // Move Torrent File
                if (!move_uploaded_file($_FILES["torrent"]["tmp_name"], "$torrent_dir/$fname")) {
                    Redirect(Url('upload'), Txt("ERROR") . ": " . Txt("UPLOAD_COULD_NOT_BE_COPIED") . " $torrent_dir - $fname");
                }

                // Parse Torrent File
                $torInfo = new Parse();
                $tor = $torInfo->torr("$torrent_dir/$fname");

                // Check External
                $announce_urls = explode(",", ANNOUNCELIST);
                if (!in_array($tor['announce'], $announce_urls)){
                    $external='yes';
                }else{
                    $external='no';
                }
                if (!CONFIG['ALLOWEXTERNAL'] && $external == 'yes') {
                    $message = Txt("UPLOAD_NO_TRACKER_ANNOUNCE");
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
                Redirect(Url('upload'), $message);
            }

            // Check Name
            if ($name == "") {
                $name = $tor['name'];
            }
            $name = str_replace(".torrent", "", $name);
            $name = str_replace("_", " ", $name);

            // Upload Images
            $allowed_types = ALLOWEDIMAGETYPES;
            $inames = array();
            for ($x = 0; $x < 2; $x++) {
                if (!($_FILES['image' . $x]['name'] == "")) {
                    $y = $x + 1;
                    if ($_FILES['image$x']['size'] > IMAGEMAXFILESIZE) {
                        Redirect(Url('upload'), Txt("INVAILD_FILE_SIZE_IMAGE"));
                    }
                    $uploaddir = UPLOADDIR . '/images/';
                    $ifile = $_FILES['image' . $x]['tmp_name'];
                    $im = getimagesize($ifile);
                    if (!$im[2]) {
                        Redirect(Url('upload'), sprintf(Txt("INVALID_IMAGE")));
                    }
                    if (!array_key_exists($im['mime'], $allowed_types)) {
                        Redirect(Url('upload'), Txt("INVALID_FILETYPE_IMAGE"));
                    }
                    $row = DB::run("SHOW TABLE STATUS LIKE 'torrents'")->fetch();
                    $next_id = $row['Auto_increment'];
                    $ifilename = $next_id . $x . $allowed_types[$im['mime']];
                    $image_name = copyImage($ifile, $uploaddir . $ifilename);
                    $inames[] = $ifilename;
                }
            }

            // Insert Torrent
            try {
                TorrentsModel::insert([
                    'filename'=>$fname, 'owner'=>$USER->has('id'), 'name'=>$name, 'vip'=>$vip, 'descr'=>$descr, 'image1'=>$inames[0], 'image2'=>$inames[1], 'category'=>$catid, 
                    'tube'=>$tube, 'added'=>get_date_time(), 'info_hash'=>$tor['hash'], 'size'=>$tor['length'], 'numfiles'=>(int) $tor['ttfilecount'], 'save_as'=>$fname,
                    'announce'=>$tor['announce'], 'external'=>$external, 'nfo'=>$nfo, 'torrentlang'=>$langid, 'anon'=>$anon, 'last_action'=>get_date_time(), 'freeleech'=>$free,
                    'tmdb'=>$tmdb, 'imdb'=>$imdb, 'announcelist'=>$announcelist, 'filelist'=>$tor['files'], 'info_hash2'=>$tor['hash2']
                ]);
            } catch (\PDOException $e) {
                TorrentsModel::deleteTorrent(DB::lastInsertId());
                rename("$torrent_dir/$fname", "$torrent_dir/duplicate.torrent"); // todo
                Redirect(Url('upload'), Txt('ACTION_FAILED'));
            }

            $id = DB::lastInsertId();

            // Rename To INT
            rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent");

            // Tags Mod
            $tags = $_POST['tags'];
            if ($tags) {
                TorrentTagModel::insert(['torrentid' => $id, 'type1' => $tags[0], 'type2' => $tags[1], 'type3' => $tags[2]]);
            }

            // Move NFO
            if ($nfo == 'yes') {
                move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo");
            }

            // Log Upload
            LogsModel::write(sprintf(Txt("TORRENT_UPLOADED"), htmlspecialchars($name), $USER->has('username')));
            
            // Shout Torrent
            $msg_shout = "New Torrent: [url=" . URLROOT . "/torrent/" . $id . "]" . $name . "[/url] has been uploaded " . ($anon == 'no' ? "by [color=".$USER->has('Color')."]" . $USER->has('username') . "[/color]" : "") . "";
            ShoutboxModel::insert(['userid'=>0, 'date'=>get_date_time(), 'user'=>'System', 'message'=>$msg_shout]);

            //Uploaded ok message
            if ($external == 'no') {
                $message = sprintf(Txt("TORRENT_UPLOAD_LOCAL"), $name, $id, $id);
            } else {
                $message = sprintf(Txt("TORRENT_UPLOAD_EXTERNAL"), $name, $id);
                // Scrape External
                Tscraper::ScrapeId($id, $tor["announce-list"], $tor['hash']);
            }

            Redirect(Url("torrent/$id"), $message);

        } else {

            Redirect(Url(), Txt("ACTION_FAILED"));

        }
    }

}