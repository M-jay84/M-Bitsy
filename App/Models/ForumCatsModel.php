<?php

namespace App\Models;

use App\Libs\DB;

/*
* Forum Read Table Data
*/
class ForumCatsModel
{
    // Delete Cat
    public function delete($id)
    {
        $stmt = DB::delete('forumcats', ['id'=>$id]);
        return $stmt;
    }

    // Insert Forum Cat
    public function insert($updateset)
    {
        $stmt = DB::insert('forumcats', $updateset);
        return $stmt;
	}
	
    // Update Forum Cat
    public function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('forumcats', $cols, $where, $vals, $other);
        return $stmt; 
	}
	
    // Fetch Forum Cat Column
    public function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forumcats', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Forum Cat Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forumcats', $columns, $where, $values, $other);
		return $stmt;
	}

    // Fetch Forum Cat Row
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('forumcats', $columns, $where, $values, $other);
		return $stmt;
	}
}