<?php

namespace App\Models;

use App\Libs\DB;

/*
* Logs Table Data
*/
class LogsModel
{
    // Add Log
    public static function write($text)
    {
        $stmt = DB::insert('log', ['added'=>get_date_time(), 'txt'=>$text]);
        return $stmt;
    }

    // Admin Get All Logs
    public static function getAll()
    {
        // Check User Input
        $search = Input('search');

        // Pagination
        $where = $search != '' ? "WHERE txt LIKE '$search%'" : '';
        $count = DB::run("SELECT COUNT(*) FROM log $where")->fetchColumn();
        list($pager, $limit) = pagination(25, $count, Url('adminlog?'));
        $logs = DB::run("SELECT id, added, txt FROM log $where ORDER BY id DESC $limit")->fetchAll();
        return array($logs, $pager);
    }

    // Admin Delete Logs
    public static function delete()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('logs', 'id', $ids);
        Redirect(Url('adminlog'), Txt("DEL_COMPLETE"));
    }

}