<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Ip;

/*
* Guest Table Data
*/
class GuestsLogic
{
    // Add Guest
    public static function add()
    {
        $time = CONFIG['TT_TIME'];
        DB::run(
            "INSERT INTO `guests` (`ip`, `time`) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `time` = ?", [Ip::getIP(), $time, $time]
        );
    }

    // Delete Guest
    public static function clean()
    {
        $past = (CONFIG['TT_TIME'] - 2400);
        DB::run("DELETE FROM `guests` WHERE `time` < ?", [$past]);
        $stmt = DB::fetchColumn('guests', 'count(ip)', '');
        return $stmt;
    }

}