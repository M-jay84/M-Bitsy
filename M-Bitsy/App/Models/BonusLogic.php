<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsLogic;
use App\Models\SnatchedLogic;
use PDO;

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
        $bonus_query = "SELECT * FROM `bonus`";
        $stmt = DB::run($bonus_query);
        return $stmt;
    }

    // Get Bonus by Post Id
    public static function getById($current_user)
    {
        $id = Validate::Input('id', 'int');

        $bonus_query = "SELECT * FROM bonus WHERE id = ?";
        $row = DB::run($bonus_query, [$id])->fetch(PDO::FETCH_ASSOC);
        if (!$row || $current_user->seedbonus < $row['cost']) {
            showMessage('bonus', LANG["NO_BONUS"]);
        }
        $users_query = "UPDATE `users` SET `seedbonus` = `seedbonus` - '$row[cost]' WHERE `id` = " . $current_user->id . "";
        DB::run($users_query);

        switch ($row['type']) {
            case 'invite':
                $users_query = "UPDATE `users` SET `invites` = `invites` + '$row[value]' WHERE `id` = " . $current_user->id . "";
                DB::run($users_query);
                break;
            case 'traffic':
                $users_query = "UPDATE `users` SET `uploaded` = `uploaded` + '$row[value]' WHERE `id` = " . $current_user->id . "";
                DB::run($users_query);
                break;
            case 'HnR':
                $uid = $current_user->id;
                if (empty($uid)) {
                    showMessage(URLROOT, LANG["NO_USER"]);
                }
                if (isset($uid)) {
                    $snatched_query = "SELECT count(id) FROM `snatched` WHERE uid=? AND hnr=?";
                    $res = DB::run($snatched_query, [$uid,'yes'])->fetchColumn();
                    if ($res == 0) {
                        showMessage(("bonus"), LANG["No HnR found for this user."]);
                    }
                    LogsLogic::write("A HnR for <a href='profile/" . $uid . "'>" . coloredname($current_user->id) . "</a> has been removed");
                    $new_modcomment = date("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . coloredname($current_user->id) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    $users_query = "UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?";
                    DB::run($users_query, [$uid]);
                    $snatched_query = "UPDATE snatched SET last_time = ? AND hnr = ? WHERE uid=?";
                    DB::run($snatched_query, [129600, 'no', $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $vipuntil = ($current_user->vipuntil != null) ? $vipuntil = date("Y-m-d H:i:s", strtotime($current_user->vipuntil) + (30 * 86400)) : $vipuntil = date("Y-m-d H:i:s", CONFIG['TT_TIME'] + (30 * 86400));
                $oldclass = ($current_user->vipuntil != null) ? $oldclass =$current_user->oldclass : $oldclass = $current_user->class;
                $users_query = "UPDATE users SET class = ? AND oldclass = ? AND vipuntil = ? WHERE id=?";
                DB::run($users_query, [3, $oldclass, $vipuntil, $current_user->id]);
                break;
        }
        showMessage('bonus', LANG["SUCCESS"]);
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
            $bonus_query = "INSERT INTO `bonus` (title, descr, cost, value, type) VALUES (?,?,?,?,?)";
            DB::run($bonus_query, [$title, $descr, $cost, $value, $type]);
        } else {
            $bonus_query = "UPDATE bonus SET title=? AND descr=? AND cost=? AND value=? AND type=?  WHERE id=?";
            DB::run($bonus_query, [$title, $descr, $cost, $value, $type, $id]);
        }

        showMessage('bonus', LANG["SUCCESS"]);
    }

    
    // Insert Bonus Or Update Bonus
    public static function deleteone($id)
    {
        $bonus_query = "DELETE FROM `bonus` WHERE id' = ?";
        DB::run($bonus_query, [$id]);
        showMessage('bonus', LANG["SUCCESS"]);
    }
}