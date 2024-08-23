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
    // Get All Users Warnings
    public function getByUserId($userid)
    {
        $stmt = DB::fetchAll('warnings', '*', ['userid='], [$userid], 'ORDER BY id DESC');
        return $stmt;
    }

    // User Warn Form Submit
    public function newwarn($input = [])
    {
        $expiretime = get_date_time(gmtime() + (86400 * $input['expiry']));
        (new WarningsModel)->insert(['userid'=>$input['userid'], 'reason'=>$input['reason'], 'added'=>get_date_time(),'expiry'=>$expiretime,'warnedby'=>$input['current_user'],'type'=>$input['type']]);
        showMessage(Url("warning/$input[userid]"), LANG["SUCCESS"]);
    }

    // Delete All Warnings
    public function deleteAll($data)
    {
        if ($data['current_user']->control_panel == "yes") {
            $count = DB::fetchColumn('warnings', 'count(id)', ['userid='], [$data['userid']]);
            $deleted = DB::deleteByIds('warnings', 'id', $data['post_id']);
            if ($count == $deleted || $count <= $deleted) {
                UsersModel::update(['warned'], ['id='], ['no', $data['userid']]);
            }
            showMessage(Url("warning/$data[userid]"), LANG["DEL_COMPLETE"]);
        }  
        showMessage(Url("warning/$data[userid]"), LANG["ACTION_FAILED"]);
    }

    // Add Warning
    public function insert($data)
    {
        DB::insert('warnings', ['userid' => $data['userid'], 'reason' => $data['reason'], 'added' => get_date_time(), 'expiry' => $data['expiry'], 'warnedby' => $data['warnedby'], 'type' => $data['type']]);
        UsersModel::update(['warned'], ['id='], ['yes', $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['userid'], 'added' => get_date_time(), 'subject' => LANG['NEW_WARNING'], 'msg' => $data['reason'], 'unread' => 'yes', 'location' => 'in']);
        (new LogsModel)->write($data['reason']);
    }

    // Update Warning
    public function update($data)
    {
        (new LogsModel)->write($data['reason']);
        UsersModel::update(['warned'], ['id='], [$data['warned'], $data['id']]);
        (new WarningsModel)->update(['expiry', 'active'], ['userid='], [get_date_time(), $data['warned'], $data['id']]);
        MessagesModel::insert(['sender' => 0, 'receiver' => $data['id'], 'added' => get_date_time(), 'msg' => $data['reason']]);
    }

    // Fetch Warning Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('warnings', $columns, $where, $values, $other)->fetchColumn();
        return $stmt;
    }

    // Get All Warnings
    public function getAll($data, $id = 0)
    {
        // Check User
        if ($data['current_user']->view_users != "yes" && $data['current_user']->id != $id) {
            showMessage(Url(), LANG["NO_USER_VIEW"]);
        }
        if (($data['current_user']->enabled == "no" || ($data['current_user']->status == "pending")) && !$data['current_user']->edit_users == 'yes') {
            showMessage(Url('members'), LANG["NO_ACCESS_ACCOUNT_DISABLED"]);
        }
        
        if ($id) {
            $stmt = DB::fetchAll('warnings', '*', ['userid='], [$id], 'ORDER BY id DESC');
            return $stmt;
        } else {
            // Pagination
            $count = UsersModel::fetchColumn('count(id)', ['enabled=', 'status=', 'warned='], ['yes', 'confirmed', 'yes']);
            list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'] . '/adminwarning?');
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

    public function deleteAllAdmin()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('warnings', ['active ='], $ids, 'userid', ['yes']);
        UsersModel::updateWhereIn(['warned' => 'no'], $ids);
        showMessage(Url('adminwarning'), LANG["SUCCESS"]);
    }

    // Delete Warnings
    public function delete($data)
    {
        DB::delete('warnings', $data);
    }
}