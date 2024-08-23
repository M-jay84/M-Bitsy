<?php

namespace App\Models;

use App\Libs\DB;

/*
* Torrent Table Data
*/
class TorrentTagModel
{
    // Update Tags
    public function insert($updateset)
    {
        $stmt = DB::insert('tagtorrent', $updateset);
        return $stmt;
    }

    // Fetch Tag Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('tagtorrent', $columns, $where, $values, $other);
        return $stmt;
    }
}