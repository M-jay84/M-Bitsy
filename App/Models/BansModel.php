<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\UsersModel;
use App\Models\LogsModel;

/*
* Bans Table Data
*/
class BansModel
{
    // Fetch All Bams
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('bans', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Admin Delete Ban
    public static function delete()
    {
        // Get Input
        $ids = multiIds('ids');

        // Delete Ban
        $bans = DB::run("SELECT * FROM bans WHERE id IN ($ids)")->fetchAll();
        foreach ($bans as $row) {
            Redirect(Url('adminban'), Txt("ACTION_COMPLETE"));
            $stmt = DB::delete('bans', ['id' => $row['id']], false);
            LogsModel::write("Ban ($row[first] - $row[last]) was removed by " . $_SESSION['id'] . " (" . $_SESSION['username'] . ")");    
        }
    }

    // Admin Add Ban
    public static function insert()
    {
        $first = Input('first');
        $last = Input('last');
        $comment = Input('comment');
        $type = Input('type');

        if ($first == "" || $type == "" || $comment == "") {
            Redirect(Url('adminban'), Txt("MISSING_FORM_DATA"));
        }

        $stmt = DB::insert('bans', ['added'=>get_date_time(),' addedby'=>$_SESSION['id'], 'first'=>$first, 'last'=>$last, 'comment'=>$comment, 'type'=>$type]);
        LogsModel::write(sprintf(Txt("BAN_ADDED"), $first, $_SESSION['username']));
        Redirect(Url('adminban'), Txt("SUCCESS"));
    }

    // Admin Get Bans
    public static function getAll($type)
    {
        if ($type == 'ip') {
            $count = UsersModel::fetchColumn('count(id)', '');
            list($pager, $limit) = pagination(50, $count, URLROOT . "/adminban/ip"); // 50 per page
            $stmt = DB::run("SELECT bans.id, bans.added, bans.addedby, bans.first, bans.last,bans.comment,bans.type, users.username 
                         FROM bans 
                         LEFT JOIN users 
                         ON bans.addedby=users.id 
                         WHERE bans.type=?
                         ORDER BY added $limit", ['ip'])->fetchAll();
        } elseif ($type == 'email') {
            $count = DB::fetchColumn('bans', 'count(id)', '');
            list($pager, $limit) = pagination(40, $count, URLROOT . "/adminban/email");
            $stmt = DB::run("SELECT bans.id, bans.added, bans.addedby, bans.first, bans.last,bans.comment,bans.type, users.username 
                         FROM bans 
                         LEFT JOIN users 
                         ON bans.addedby=users.id 
                         WHERE bans.type=? OR bans.type=?
                         ORDER BY added $limit", ['email', 'domain'])->fetchAll();
        }

        $array = [
            'bans' => $stmt,
            'pager' => $pager
        ];

        return $array;
    }

    // Check Email account/invite
    public static function checkEmail($email)
    {
        $maildomain = (substr($email, strpos($email, "@") + 1));
        $a = DB::fetchColumn('bans', 'COUNT(id)', ['first ='], [$email]);
        if ($a != 0) {
            $message = sprintf(Txt("EMAIL_ADDRESS_BANNED_S"), $email);
        }
        $a = DB::fetchColumn('bans', 'count(*)', 'first LIKE ', ['%$maildomain%']);
        if ($a != 0) {
            $message = sprintf(Txt("EMAIL_ADDRESS_BANNED_S"), $email);
        }
        // check if email addy is already in use
        $a = UsersModel::fetchColumn('COUNT(id)', ['email ='], [$email]);
        if ($a != 0) {
            $message = sprintf(Txt("EMAIL_ADDRESS_INUSE_S"), $email);
        }
        return $message;
    }

}