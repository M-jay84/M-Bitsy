<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Ip;

/*
* Guest Table Data
*/
class GuestsModel
{
    // Add Guest
    public function add()
    {
        $time = gmtime();
        DB::run(
            "INSERT INTO `guests` (`ip`, `time`) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `time` = ?", [Ip::getIP(), $time, $time]
        );
    }

    // Delete Guest
    public function clean()
    {
        $past = (gmtime() - 2400);
        DB::run("DELETE FROM `guests` WHERE `time` < ?", [$past]);
        $stmt = DB::fetchColumn('guests', 'count(ip)', '');
        return $stmt;
    }

}