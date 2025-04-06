<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ScrapeLogic;

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
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
            if (ini_get('output_handler') != 'ob_gzhandler') {
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        } else {
            ob_start();
        }

        $query = ScrapeLogic::internal();
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
        ScrapeLogic::internal();
    }
}