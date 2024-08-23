<?php

namespace App\Libs;

use App\Libs\Parse;
use App\Libs\Scraper;
use App\Libs\DB;

/*
* Scrape Torrent
*/
class Tscraper
{

    // Scrape Torrent
    public static function ScrapeId($id, $annlist, $infohash)
    {
        if (!is_array($annlist)) {
            $torInfo = new Parse();
            $tor = $torInfo->torr(CONFIG['UPLOADDIR']."/torrents/$id.torrent");
            $infohash = $tor['hash'];
            $infohash2 = $tor['hash2'];
            $annlist = $tor['announce-list'];
            $var2 = serialize($annlist); 
        }
        
        $scraper = new Scraper();
        $scraped = $scraper->scrape($infohash, $annlist, 20, 4, true );

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
            DB::update('torrents', ['leechers','seeders','times_completed','last_action','visible','announcelist'], ['id='],
                                      [$leechers, $seeders, $completed, get_date_time(), 'yes', $var2, $id]
            );
        } else {
            // Its Dead :(
            DB::update('torrents', ['last_action'], ['id='], [get_date_time(), $id]);
        }
    }

    // Scrape Torrent Batch/All todo
    public static function scrapeall()
    {
        // Set A Limit ? how fast is server / how many torrents to limit ?
        //set_time_limit(15);

        // Rescrape 10 Torrents Every X Days. (Default: 2 days)
        $stmt = DB::fetchAll('torrents', ['id','info_hash','last_action','announcelist'],
                                ['external=', 'last_action <='],
                                ['yes', 86400], 'DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 DAY)', "LIMIT 10");

        foreach ($stmt as $tor) {
            self::ScrapeId($tor['id'], $tor['announcelist'], $tor['info_hash']);
        }
    }

}