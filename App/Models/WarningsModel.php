<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\UsersModel;
use App\Models\MessagesModel;
use App\Models\LogsModel;

/*
* Warnings Table Data
*/
class WarningsModel
{
    // Add Warning
    public static function insert($data)
    {
        DB::insert('warnings', ['userid' => $data['userid'], 'reason' => $data['reason'], 'added' => get_date_time(), 'expiry' => $data['expiry'], 'warnedby' => $data['warnedby'], 'type' => $data['type']]);
        UsersModel::update(['warned'], ['id='], ['yes', $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['userid'], 'added' => get_date_time(), 'subject' => Txt('NEW_WARNING'), 'msg' => $data['reason'], 'unread' => 'yes', 'location' => 'in']);
        LogsModel::write($data['reason']);
    }

    // Update Warning
    public static function update($data)
    {
        LogsModel::write($data['reason']);
        UsersModel::update(['warned'], ['id='], [$data['warned'], $data['id']]);
        WarningsModel::update(['expiry', 'active'], ['userid='], [get_date_time(), $data['warned'], $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['id'], 'added' => get_date_time(), 'msg' => $data['reason']]);
    }

    // Fetch Warning Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('warnings', $columns, $where, $values, $other)->fetchColumn();
        return $stmt;
    }

    // Fetch Warning Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('warnings', $columns, $where, $values, $other);
        return $stmt;
    }

    // Get All Warnings
    public static function getAll($USER, $id = 0)
    {
        // Check User
        if (!$USER->permission('view_users', $id)) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }
        if (($USER->has('enabled') == "no" || ($USER->has('status') == "pending")) && !$USER->permission('edit_users')) {
            Redirect(Url('members'), Txt("NO_ACCESS_ACCOUNT_DISABLED"));
        }
        
        if ($id) {
            $stmt = DB::fetchAll('warnings', '*', ['userid='], [$id], 'ORDER BY id DESC');
            $count = $stmt->rowCount();
            $array = [
                'count' => $count,
                'stmt' => $stmt
            ];
            return $array;
        } else {
            // Pagination
            $count = UsersModel::fetchColumn('count(id)', ['enabled=', 'status=', 'warned='], ['yes', 'confirmed', 'yes']);
            list($pager, $limit) = pagination(25, $count, URLROOT . '/adminwarning?');
            $stmt = DB::run("SELECT users.id,users.username,users.class,users.added,users.last_access, 
                           (
                            SELECT COUNT(*)
                            FROM warnings 
                            WHERE warnings.userid = users.id 
                            AND active = ?
                            ) as num
                            FROM users
                            WHERE enabled = ?
                            AND status = ? 
                            AND warned = ?
                            ORDER BY `added` 
                            DESC $limit", ['yes', 'yes', 'confirmed', 'yes'])->fetchAll();
        }
        $array = [
            'count' => $count,
            'stmt' => $stmt,
            'pager' => $pager
        ];
        return $array;
    }

    // Delete All Warnings
    public static function deleteAll($USER, $id)
    {
        $ids = multiIds("ids");
        if ($USER->permission('edit_users') && $USER->has('control_panel') == "yes") {
            $count = DB::fetchColumn('warnings', 'count(id)', ['userid='], [$id]);
            $deleted = DB::deleteByIds('warnings', 'id', $ids);
            if ($count == $deleted || $count <= $deleted) {
                UsersModel::update(['warned'], ['id='], ['no', $id]);
            }
        }  
        Redirect($_SERVER['HTTP_REFERER'], Txt("DEL_COMPLETE"));
    }

    public static function deleteAllAdmin()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('warnings', ['active ='], $ids, 'userid', ['yes']);
        UsersModel::updateWhereIn(['warned' => 'no'], $ids);
        Redirect(Url('adminwarning'), Txt("SUCCESS"));
    }

    // Delete Warnings
    public static function delete($data)
    {
        DB::delete('warnings', $data);
    }

    // User Warn Form Submit
    public static function newwarn($USER, $userid)
    {
        // Check User Input
        $reason = Input('reason');
        $expiry = Input('expiry', 'int');
        $type = Input('type');
        
        // Check Input
        if (!$reason || !$expiry || !$type) {
            Redirect(Url("profile/$userid"), Txt("MISSING_FORM_DATA"));
        }

        if (!$USER->permission('edit_users')) {
            Redirect(Url("profile/$userid"), Txt("NO_PERMISSION"));
        }

        $expiretime = get_date_time(gmtime() + (86400 * $expiry));

        // Insert Warning
        WarningsModel::insert(['userid'=>$userid, 'reason'=>$reason, 'added'=>get_date_time(),'expiry'=>$expiretime,'warnedby'=>$USER->has('id'),'type'=>$type]);

        Redirect(Url("warning/$userid"), Txt("SUCCESS"));
    }
}