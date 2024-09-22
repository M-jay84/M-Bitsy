<?php

namespace App\Models;

use App\Libs\DB;

/*
* Task Table Data
*/
class TasksLogic
{
    // Get Version
    public static function version()
    {
        $stmt = DB::run("SELECT VERSION() AS version")->fetch();
        return $stmt;
    }

    // Update Task
    public static function update($cols, $where, $vals, $other = '')
    {
        $stmt = DB::update('tasks', $cols, $where, $vals, $other);
        return $stmt;
	}
	
    // Fetch Task Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('tasks', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Insert Task
    public static function insert($updateset)
    {
        $stmt = DB::insert('tasks', $updateset);
        return $stmt;
	}

    // Insert Task
    public static function clearshoutbox($sure = '')
    {
        ShoutboxLogic::truncate($sure);
	}
	
}