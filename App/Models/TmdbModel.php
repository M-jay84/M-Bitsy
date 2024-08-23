<?php

namespace App\Models;

use App\Libs\DB;

/*
* TMDB Table Data
*/
class TmdbModel
{
    // Insert TMDB Details
    public function insert($updateset)
    {
        $stmt = DB::insert('tmdb', $updateset);
        return $stmt;
	}

    // Fetch TMDB Column
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('tmdb', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch TMDB Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('tmdb', $columns, $where, $values, $other);
		return $stmt;
	}
}