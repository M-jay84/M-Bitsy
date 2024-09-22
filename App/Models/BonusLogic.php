<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsLogic;
use App\Models\SnatchedLogic;

/*
* Bonus Table Data
*/
class BonusLogic
{

    public static function interval()
    {
        $interval = floor(CONFIG['ADDBONUS'] / 60);
        return $interval;
    }
    

    // Get Bonus All Data
    public static function getAll() 
    {
        $stmt = DB::fetchAll('bonus', '*');
        return $stmt;
    }

    // Get Bonus by Post Id
    public static function getById($current_user)
    {
        $id = Validate::Input('id', 'int');

        $row = DB::fetchRow('bonus', '*', ['id ='], [$id]);
        if (!$row || $current_user->seedbonus < $row['cost']) {
            showMessage('bonus', LANG["NO_BONUS"]);
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
                    showMessage(URLROOT, LANG["NO_USER"]);
                }
                if (isset($uid)) {
                    $res = SnatchedLogic::fetchColumn('count(id)', ['uid=', 'hnr='], [$uid,'yes']);
                    if ($res == 0) {
                        showMessage(("bonus"), LANG["No HnR found for this user."]);
                    }
                    LogsLogic::write("A HnR for <a href='profile/" . $uid . "'>" . coloredname($current_user->id) . "</a> has been removed");
                    $new_modcomment = date("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . coloredname($current_user->id) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?", [$uid]);
                    SnatchedLogic::update(['last_time', 'hnr'], ['uid='], [129600, 'no', $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $vipuntil = ($current_user->vipuntil != null) ? $vipuntil = date("Y-m-d H:i:s", strtotime($current_user->vipuntil) + (30 * 86400)) : $vipuntil = date("Y-m-d H:i:s", CONFIG['TT_TIME'] + (30 * 86400));
                $oldclass = ($current_user->vipuntil != null) ? $oldclass =$current_user->oldclass : $oldclass = $current_user->class;
                DB::update('users', ['class','oldclass','vipuntil'], ['id='], [3, $oldclass, $vipuntil, $current_user->id]);
                break;
        }
    }

    // Delete Id
    public static function delete($id)
    {
        DB::delete('bonus', ['id' => $id]);
    }

    // Insert Bonus Or Update Bonus
    public static function insertOrUpdate()
    {
        $id = Validate::Input('id', 'int');
        $title = Validate::Input('title');
        $descr = Validate::Input('descr');
        $cost = Validate::Input('cost', 'int');
        $value = Validate::Input('value');
        $type = Validate::Input('type');
    
        if (empty($title) or empty($descr) or empty($type) or !is_numeric($cost)) {
            showMessage('bonus', LANG["MISSING_FORM_DATA"]);
        }
    
        if (!$id) {
            DB::insert('bonus', ['title' => $title, 'descr' => $descr, 'cost' => $cost, 'value' => $value, 'type' => $type]);
        } else {
            DB::update('bonus', ['title','descr','cost','value','type'], ['id='], [$title, $descr, $cost, $value, $type, $id]);
        }
    }
}