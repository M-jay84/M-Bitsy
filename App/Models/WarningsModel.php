<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\UsersModel;
use App\Models\MessagesModel;
use App\Models\LogsModel;

/*
* Warnings Table Data
*/
class WarningsModel
{
    // Get All Users Warnings
    public static function getByUserId($userid)
    {
        $stmt = DB::fetchAll('warnings', '*', ['userid='], [$userid], 'ORDER BY id DESC');
        return $stmt;
    }

    // User Warn Form Submit
    public static function newwarn($userid)
    {
        // Check User Input
        $input = [
            'reason' => Validate::Input('reason'),
            'expiry' => Validate::Input('expiry', 'int'),
            'type' => Validate::Input('type'),
            'userid' => $userid
        ];

        // Check Input
        if (!$input['reason'] || !$input['expiry'] || !$input['type']) {
            showMessage("profile/$input[userid]", LANG["MISSING_FORM_DATA"]);
        }


        $expiretime = date("Y-m-d H:i:s", CONFIG['TT_TIME'] + (86400 * $input['expiry']));
        WarningsModel::insert(['userid'=>$input['userid'], 'reason'=>$input['reason'], 'added'=>CONFIG['TT_DATE'],'expiry'=>$expiretime,'warnedby'=>'staff','type'=>$input['type']]);
        showMessage(("warning/$input[userid]"), LANG["SUCCESS"]);
    }

    // Delete All Warnings
    public static function deleteAll($data)
    {
        if ($data['current_user']->control_panel == "yes") {
            $count = DB::fetchColumn('warnings', 'count(id)', ['userid='], [$data['userid']]);
            $deleted = DB::deleteByIds('warnings', 'id', $data['post_id']);
            if ($count == $deleted || $count <= $deleted) {
                DB::update('users', ['warned'], ['id='], ['no', $data['userid']]);
            }
            showMessage(("warning/$data[userid]"), LANG["DEL_COMPLETE"]);
        }  
        showMessage(("warning/$data[userid]"), LANG["ACTION_FAILED"]);
    }

    // Add Warning
    public static function insert($data)
    {
        DB::insert('warnings', ['userid' => $data['userid'], 'reason' => $data['reason'], 'added' => CONFIG['TT_DATE'], 'expiry' => $data['expiry'], 'warnedby' => $data['warnedby'], 'type' => $data['type']]);
        DB::update('users', ['warned'], ['id='], ['yes', $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['userid'], 'added' => CONFIG['TT_DATE'], 'subject' => LANG['NEW_WARNING'], 'msg' => $data['reason'], 'unread' => 'yes', 'location' => 'in']);
        LogsModel::write($data['reason']);
    }

    // Update Warning
    public static function update($data)
    {
        LogsModel::write($data['reason']);
        DB::update('users', ['warned'], ['id='], [$data['warned'], $data['id']]);
        WarningsModel::update(['expiry', 'active'], ['userid='], [CONFIG['TT_DATE'], $data['warned'], $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['id'], 'added' => CONFIG['TT_DATE'], 'msg' => $data['reason']]);
    }

    // Fetch Warning Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('warnings', $columns, $where, $values, $other)->fetchColumn();
        return $stmt;
    }

    // Get All Warnings
    public static function getAll($data, $id = 0)
    {
        // Check User
        if ($data['current_user']->view_users != "yes" && $data['current_user']->id != $id) {
            showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }
        if (($data['current_user']->enabled == "no" || ($data['current_user']->status == "pending")) && !$data['current_user']->edit_users == 'yes') {
            showMessage(('members'), LANG["NO_ACCESS_ACCOUNT_DISABLED"]);
        }
        
        if ($id) {
            $stmt = DB::fetchAll('warnings', '*', ['userid='], [$id], 'ORDER BY id DESC');
            return $stmt;
        } else {
            // Pagination
            $count = DB::fetchColumn('users', 'count(id)', ['enabled=', 'status=', 'warned='], ['yes', 'confirmed', 'yes']);
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

    public static function deleteAllAdmin()
    {
        $ids = Validate::multiIds('ids');
        DB::deleteByIds('warnings', ['active ='], $ids, 'userid', ['yes']);
        DB::updateWhereIn('users', ['warned' => 'no'], $ids);
        showMessage(('adminwarning'), LANG["SUCCESS"]);
    }

    // Delete Warnings
    public static function delete($data)
    {
        DB::delete('warnings', $data);
    }
}