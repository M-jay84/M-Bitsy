<?php

namespace App\Models;

use App\Libs\DB;

/*
* Forum Read Table Data
*/
class ReadLogic
{
    // Delete Read
    public static function delete($data)
    {
        $stmt = DB::delete('forum_readposts', $data);
        return $stmt;
    }

    // Insert Forum Read
    public static function insert($updateset)
    {
        $stmt = ReadLogic::insert('forum_readposts', $updateset);
        return $stmt;
	}
	
    // Update Forum Read
    public static function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('forum_readposts', $cols, $where, $vals, $other);
        return $stmt;
	}
	
    // Fetch Forum Read Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forum_readposts', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Forum Read Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_readposts', $columns, $where, $values, $other);
		return $stmt;
	}
}