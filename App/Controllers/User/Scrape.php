<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Torrents\Parse;
use App\Libs\Torrents\Scraper;
use App\Models\TorrentsModel;

/*
* Scrape Controller
*/
class Scrape extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    // Scrape Internal Torrent
    public function index()
    {
        //disable error reporting
        error_reporting(0);

        // check if client can handle gzip
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
            if (ini_get('output_handler') != 'ob_gzhandler') {
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        } else {
            ob_start();
        }

        $infohash = array();
        foreach (explode("&", $_SERVER["QUERY_STRING"]) as $item) {
            if (preg_match("#^info_hash=(.+)\$#", $item, $m)) {
                $hash = urldecode($m[1]);
                $info_hash = stripslashes($hash);
                if (strlen($info_hash) == 20) {
                    $info_hash = bin2hex($info_hash);
                } elseif (strlen($info_hash) != 40) {
                    continue;
                }
                $infohash[] = strtolower($info_hash);
            }
        }

        if (!count($infohash)) {
            die("Invalid infohash.");
        }

        $hashes = join(",", $infohash);
        $query = TorrentsModel::scrapeInfo($hashes);
        $result = "d5:filesd";

            $result .= "20:" . hex2bin($$query['info_hash']) . "d";
            $result .= "8:completei" . $query['seeders'] . "e";
            $result .= "10:downloadedi" . $query['times_completed'] . "e";
            $result .= "10:incompletei" . $query['leechers'] . "e";
            $result .= "4:name" . strlen($query['filename']) . ":" . $query['filename'] . "e";
            $result .= "e";


        $result .= "ee";
        echo $result;
        ob_end_flush();
    }

    // Scrape External Torrent Submit
    public function external()
    {
        $id = (isset($_GET['id'])) ? (int) $_GET['id'] : 0;

        $resu = TorrentsModel::fetchRow('id, info_hash, announce, announcelist', ['external =','id ='], ['yes',$id]);
        
        while ($rowu = $resu) {
            // No need parsing but lets update any old torrents first
            if ($rowu['announcelist'] == '') {
                $tor = Parse::torr(UPLOAD_PATH."/torrents/$rowu[id].torrent");
                $infohash = $tor['hash'];
                $infohash2 = $tor['hash2'];
                $annlist = $tor['announce-list'];
                $newannouncelist = serialize($annlist);
            } else {
                $infohash = $rowu['info_hash'];
                $annlist = unserialize($rowu['announcelist']);
                $newannouncelist = $rowu['announcelist'];
            }
            
            $scraper = new Scraper();
            $scraped = $scraper->scrape($infohash, $annlist, 20, 4, true );

            if ( $scraper->has_errors() ) {
                $error = implode(",", $scraper->get_errors());
                showMessage("torrent/$id", LANG[$error]);
            }

            $myarray = array_shift($scraped);

            $seeders = $leechers = $completed = 0;
            if ($myarray['seeders'] > 0) {
                $seeders = $myarray['seeders'];
            }
            if ($myarray['leechers'] > 0) {
                $leechers = $myarray['leechers'];
            }
            if ($myarray['completed'] > 0) {
                $completed = $myarray['completed'];
            }

            if ($seeders !== 0) {
                // Update the Torrent
                TorrentsModel::update(['leechers','seeders','times_completed','last_action','visible','announcelist'], ['id ='],
                [$leechers, $seeders, $completed, CONFIG['TT_DATE'], 'yes', $newannouncelist, $rowu['id']]);

            } else {
                // Its Dead :(
                TorrentsModel::update(['last_action'], ['id='], [CONFIG['TT_DATE'], $rowu['id']]);
            }

            // Redirect with message
            if ($seeders !== 0) {
                showMessage("torrent/$id", LANG["UPDATE_COMPLETE"]);
            } else {
                showMessage("torrent/$id", LANG["SCRAPE_DEAD"]);
            }
        }
    }

}