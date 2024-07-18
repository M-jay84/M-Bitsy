<?php

namespace App\Models;

use App\Libs\DB;

/*
* Pollanswers Table Data
*/
class PollsAnswerModel
{
    // Fetch All Answers
    public static function getAnswers($id)
    {
        $stmt = DB::run("SELECT pollanswers.pollid, pollanswers.userid, pollanswers.selection FROM pollanswers  
            LEFT OUTER JOIN polls 
            ON pollanswers.pollid=polls.id
            WHERE polls.id=?
            ORDER BY pollid DESC",
            [$id]
        )->fetchAll();
        return $stmt;
    }

    // Fetch Torrent Details
    public static function insert($updateset)
    {
        $stmt = DB::insert('pollanswers', $updateset);
        return $stmt;
    }

    // Fetch Poll Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('pollanswers', $columns, $where, $values, $other)->fetchColumn();
        return $stmt;
    }

    // Fetch All Answers
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('pollanswers', $columns, $where, $values, $other);
        return $stmt;
    }

    // Fetch Poll Answer Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('pollanswers', $columns, $where, $values, $other);
        return $stmt;
    }

    // Delete Poll
    public static function delete($id)
    {
        $stmt = DB::delete('pollanswers', ['pollid'=>$id]);
        return $stmt;
    }

}