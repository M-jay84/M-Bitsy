<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\SnatchedModel;
use App\Models\LogsModel;
use App\Models\UsersModel;

/*
* Bonus Table Data
*/
class BonusModel
{
    // Get Bonus All Data
    public function getAll() 
    {
        $stmt = DB::fetchAll('bonus', '*');
        return $stmt;
    }

    // Get Bonus by Post Id
    public function getById($current_user)
    {
        $id = Input('id', 'int');

        $row = DB::fetchRow('bonus', '*', ['id ='], [$id]);
        if (!$row || $current_user->seedbonus < $row['cost']) {
            showMessage(Url('bonus'), LANG["NO_BONUS"]);
        }
        DB::run("UPDATE `users` SET `seedbonus` = `seedbonus` - '$row[cost]' WHERE `id` = " . $current_user->id . "");

        switch ($row['type']) {
            case 'invite':
                DB::run("UPDATE `users` SET `invites` = `invites` + '$row[value]' WHERE `id` = " . $current_user->id . "");
                break;
            case 'traffic':
                DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row[value]' WHERE `id` = " . $current_user->id . "");
                break;
            case 'HnR':
                $uid = $current_user->id;
                if (empty($uid)) {
                    showMessage(Url(), LANG["NO_USER"]);
                }
                if (isset($uid)) {
                    $res = (new SnatchedModel)->fetchColumn('count(id)', ['uid=', 'hnr='], [$uid,'yes']);
                    if ($res == 0) {
                        showMessage(Url("bonus"), LANG["No HnR found for this user."]);
                    }
                    (new LogsModel)->write("A HnR for <a href='profile/" . $uid . "'>" . coloredname($current_user->id) . "</a> has been removed");
                    $new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . coloredname($current_user->id) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?", [$uid]);
                    (new SnatchedModel)->update(['last_time', 'hnr'], ['uid='], [129600, 'no', $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $vipuntil = ($current_user->vipuntil != null) ? $vipuntil = get_date_time(strtotime($current_user->vipuntil) + (30 * 86400)) : $vipuntil = get_date_time(gmtime() + (30 * 86400));
                $oldclass = ($current_user->vipuntil != null) ? $oldclass =$current_user->oldclass : $oldclass = $current_user->class;
                UsersModel::update(['class','oldclass','vipuntil'], ['id='], [3, $oldclass, $vipuntil, $current_user->id]);
                break;
        }
    }

    // Delete Id
    public function delete($id)
    {
        DB::delete('bonus', ['id' => $id]);
    }

    // Insert Bonus Or Update Bonus
    public function insertOrUpdate()
    {
        $id = Input('id', 'int');
        $title = Input('title');
        $descr = Input('descr');
        $cost = Input('cost', 'int');
        $value = Input('value');
        $type = Input('type');
    
        if (empty($title) or empty($descr) or empty($type) or !is_numeric($cost)) {
            showMessage(Url('bonus'), LANG["MISSING_FORM_DATA"]);
        }
    
        if (!$id) {
            DB::insert('bonus', ['title' => $title, 'descr' => $descr, 'cost' => $cost, 'value' => $value, 'type' => $type]);
        } else {
            DB::update('bonus', ['title','descr','cost','value','type'], ['id='], [$title, $descr, $cost, $value, $type, $id]);
        }
    }
}