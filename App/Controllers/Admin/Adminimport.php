<?php

namespace App\Controllers\Admin;

use App\Libs\Parse;
use App\Models\CatagoriesModel;
use App\Models\TorrentLangModel;
use App\Models\LogsModel;
use App\Libs\Tscraper;
use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Import Controller
*/
class Adminimport extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Import Default Page
    public function index()
    {
    
        //ini_set("upload_max_filesize",$max_torrent_size);
        $files = gettorrentfiles();
        
        // Check User
        if (!$this->current_user->edit_torrents == 'yes') {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }

        $categorys = CatagoriesModel::dropdown($_GET["cat"]);
        $languages = TorrentLangModel::langList();

        // Data For View
        $data = [
            'page_title' => LANG["IMPORT"],
            'V_files' => $files,
            'V_categorys' => $categorys,
            'V_languages' => $languages
        ];

        // Load View
        $this->view('import', $data, 'admin');
    }

    // Import Form Submit
    public function submit() // todo full file
    {
    
        $files = gettorrentfiles();
        //generate announce_urls[] from config.php
        $announce_urls = explode(",", strtolower(CONFIG['URLROOT'] . CONFIG['ANNOUNCELIST']));
        set_time_limit(0);
        
        // Check User
        if (!$this->current_user->edit_torrents == 'yes') {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }

        // Check Correct Input
        $catid = Input('type', 'int');
        if (!Id($catid)) {
            $message = LANG["UPLOAD_NO_CAT"];
        }

        if (empty($message)) {
            $r = (new CatagoriesModel)->fetchRow('name, parent_cat', ['id ='], [$catid]);

            require_once "assets/themes/" . ($this->current_user->stylesheet ?: CONFIG['DEFAULTTHEME']) . "/header.php";
            block_begin(LANG["UPLOAD_COMPLETE"]);
            echo "<center>";
            echo "<b>Category:</b> " . htmlspecialchars($r['parent_cat']) . " -> " . htmlspecialchars($r['name']) . "<br />";
            for ($i = 0; $i < count($files); $i++) {
                $fname = $files[$i];
                $descr = LANG["UPLOAD_NO_DESC"];
                $langid = Input('language', 'int');
                preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                $shortfname = $torrent = $matches[1];

                //parse torrent file
                $torrent_dir = CONFIG['UPLOADDIR']."/torrents";
                $torInfo = new Parse();
                $tor = $torInfo->torr(CONFIG['UPLOADDIR']."/import/$fname");

                $message = "<br /><br /><hr /><br /><b>$tor[name]</b><br /><br />Name: " . htmlspecialchars($fname) . "<br />message: ";
                //check announce url is local or external
                $announce_urls = explode(",", CONFIG['URLROOT'] . CONFIG['ANNOUNCELIST']);
                if (!in_array($tor['announce'], $announce_urls)) {
                    $external = 'yes';
                } else {
                    $external = 'no';
                }

                if ($external === 'yes') {
                    $announcelist = serialize($tor["announce-list"]);
                }

                if (!CONFIG['ALLOWEXTERNAL'] && $external == 'yes') {
                    $message .= LANG["UPLOAD_NO_TRACKER_ANNOUNCE"];
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

                $id = (new TorrentsModel)->insert([
                    'filename'=>$fname, 'owner'=>$this->current_user->id, 'name'=>$name, 'descr'=>$descr, 'category'=>$catid, 
                    'added'=>get_date_time(), 'info_hash'=>$tor['hash'], 'size'=>$tor['length'], 'numfiles'=>(int) $tor['ttfilecount'], 'save_as'=>$fname,
                    'announce'=>$tor['announce'], 'external'=>$external, 'torrentlang'=>$langid, 'anon'=>$anon, 'last_action'=>get_date_time(),
                    'announcelist'=>$announcelist, 'filelist'=>$tor['files'], 'info_hash2'=>$tor['hash2']
                ]);

                if ($id == 0) {
                    $message .= LANG["UPLOAD_NO_ID"];
                    echo $message;
                    continue;
                }

                copy(CONFIG['UPLOADDIR']."/import/$files[$i]", "$torrent_dir/$id.torrent");

                //EXTERNAL SCRAPE
                if ($external == 'yes' && CONFIG['UPLOADSCRAPE']) {
                    Tscraper::ScrapeId($id, $tor["announce-list"], $tor['hash']);
                }

                (new LogsModel)->write("Torrent $id ($name) was Uploaded by Visitor(username]");
                $message .= "<br /><b>" . LANG["UPLOAD_COMPLETE"] . "</b><br /><a href='" . CONFIG['URLROOT'] . "/torrent/" . $id . "'>" . LANG["UPLOAD_VIEW_DL"] . "</a><br /><br />";
                echo $message;
                @unlink(CONFIG['UPLOADDIR']."/import/$fname");
            }
            echo "</center>";
            block_end();
            require_once "assets/themes/" . ($this->current_user->stylesheet ?: CONFIG['DEFAULTTHEME']) . "/footer.php";
            die;

        } else {
            showMessage(Url(), $message);
        }

    }

}