<?php

namespace App\Controllers\Admin;

use App\Libs\Parse;
use App\Models\CatagoriesModel;
use App\Models\TorrentLangModel;
use App\Models\LogsModel;
use App\Libs\Tscraper;
use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Import Controller
*/
class Adminimport 
{
    // Import Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        //ini_set("upload_max_filesize",$max_torrent_size);
        $files = gettorrentfiles();
        
        // Check User
        if (!$USER->permission('edit_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        $categorys = CatagoriesModel::dropdown($_GET["cat"]);
        $languages = TorrentLangModel::langList();

        // Init Data
        $data = [
            'V_title' => Txt("IMPORT"),
            'V_files' => $files,
            'V_categorys' => $categorys,
            'V_languages' => $languages,
            'USER' => $USER
        ];

        // Load View
        template('import', $data, 'admin');
    }

    // Import Form Submit
    public function submit() // todo full file
    {
        $USER = new User(_MODERATOR, 2);
        $files = gettorrentfiles();
        //generate announce_urls[] from config.php
        $announce_urls = explode(",", strtolower(ANNOUNCELIST));
        set_time_limit(0);
        
        // Check User
        if (!$USER->permission('edit_torrents')) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        // Check Correct Input
        $catid = Input('type', 'int');
        if (!Id($catid)) {
            $message = Txt("UPLOAD_NO_CAT");
        }

        if (empty($message)) {
            $r = CatagoriesModel::fetchRow('name, parent_cat', ['id ='], [$catid]);

            require_once "assets/themes/" . ($USER->has('stylesheet') ?: CONFIG['DEFAULTTHEME']) . "/header.php";
            block_begin(Txt("UPLOAD_COMPLETE"));
            echo "<center>";
            echo "<b>Category:</b> " . htmlspecialchars($r['parent_cat']) . " -> " . htmlspecialchars($r['name']) . "<br />";
            for ($i = 0; $i < count($files); $i++) {
                $fname = $files[$i];
                $descr = Txt("UPLOAD_NO_DESC");
                $langid = Input('language', 'int');
                preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                $shortfname = $torrent = $matches[1];

                //parse torrent file
                $torrent_dir = UPLOADDIR."/torrents";
                $torInfo = new Parse();
                $tor = $torInfo->torr(UPLOADDIR."/import/$fname");

                $message = "<br /><br /><hr /><br /><b>$tor[name]</b><br /><br />Name: " . htmlspecialchars($fname) . "<br />message: ";
                //check announce url is local or external
                $announce_urls = explode(",", ANNOUNCELIST);
                if (!in_array($tor['announce'], $announce_urls)) {
                    $external = 'yes';
                } else {
                    $external = 'no';
                }

                if ($external === 'yes') {
                    $announcelist = serialize($tor["announce-list"]);
                }

                if (!CONFIG['ALLOWEXTERNAL'] && $external == 'yes') {
                    $message .= Txt("UPLOAD_NO_TRACKER_ANNOUNCE");
                    echo $message;
                    continue;
                }

                $name = $tor['name'];
                $name = str_replace(".torrent", "", $name);
                $name = str_replace("_", " ", $name);

                //anonymous upload
                $anonyupload = Input('anonycheck');
                if ($anonyupload == "yes") {
                    $anon = "yes";
                } else {
                    $anon = "no";
                }

                $id = TorrentsModel::insert([
                    'filename'=>$fname, 'owner'=>$USER->has('id'), 'name'=>$name, 'descr'=>$descr, 'category'=>$catid, 
                    'added'=>get_date_time(), 'info_hash'=>$tor['hash'], 'size'=>$tor['length'], 'numfiles'=>(int) $tor['ttfilecount'], 'save_as'=>$fname,
                    'announce'=>$tor['announce'], 'external'=>$external, 'torrentlang'=>$langid, 'anon'=>$anon, 'last_action'=>get_date_time(),
                    'announcelist'=>$announcelist, 'filelist'=>$tor['files'], 'info_hash2'=>$tor['hash2']
                ]);

                if ($id == 0) {
                    $message .= Txt("UPLOAD_NO_ID");
                    echo $message;
                    continue;
                }

                copy(UPLOADDIR."/import/$files[$i]", "$torrent_dir/$id.torrent");

                //EXTERNAL SCRAPE
                if ($external == 'yes' && CONFIG['UPLOADSCRAPE']) {
                    Tscraper::ScrapeId($id, $tor["announce-list"], $tor['hash']);
                }

                LogsModel::write("Torrent $id ($name) was Uploaded by Visitor(username]");
                $message .= "<br /><b>" . Txt("UPLOAD_COMPLETE") . "</b><br /><a href='" . URLROOT . "/torrent/" . $id . "'>" . Txt("UPLOAD_VIEW_DL") . "</a><br /><br />";
                echo $message;
                @unlink(UPLOADDIR."/import/$fname");
            }
            echo "</center>";
            block_end();
            require_once "assets/themes/" . ($USER->has('stylesheet') ?: CONFIG['DEFAULTTHEME']) . "/footer.php";
            die;

        } else {
            Redirect(Url(), $message);
        }

    }

}