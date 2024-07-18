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
    public static function getAll() 
    {
        $stmt = DB::fetchAll('bonus', '*');
        return $stmt;
    }

    // Get Bonus by Post Id
    public static function getById($USER)
    {
        $id = Input('id', 'int');

        $row = DB::fetchRow('bonus', '*', ['id ='], [$id]);
        if (!$row || $USER->has('seedbonus') < $row['cost']) {
            Redirect(Url('bonus'), Txt("NO_BONUS"));
        }
        DB::run("UPDATE `users` SET `seedbonus` = `seedbonus` - '$row[cost]' WHERE `id` = " . $USER->has('id') . "");

        switch ($row['type']) {
            case 'invite':
                DB::run("UPDATE `users` SET `invites` = `invites` + '$row[value]' WHERE `id` = " . $USER->has('id') . "");
                break;
            case 'traffic':
                DB::run("UPDATE `users` SET `uploaded` = `uploaded` + '$row[value]' WHERE `id` = " . $USER->has('id') . "");
                break;
            case 'HnR':
                $uid = $USER->has('id');
                if (empty($uid)) {
                    Redirect(Url(), Txt("NO_USER"));
                }
                if (isset($uid)) {
                    $res = SnatchedModel::fetchColumn('count(id)', ['uid=', 'hnr='], [$uid,'yes']);
                    if ($res == 0) {
                        Redirect("bonus", Txt("No HnR found for this user."));
                    }
                    LogsModel::write("A HnR for <a href='profile/" . $uid . "'>" . coloredname($USER->has('id')) . "</a> has been removed");
                    $new_modcomment = gmdate("d-m-Y \à H:i") . " - ";
                    $new_modcomment .= "" . coloredname($USER->has('id')) . " has cleared H&R for " . $row['cost'] . " points \n";
                    $modcom = $new_modcomment;
                    DB::run("UPDATE `users` SET `modcomment` = CONCAT($modcom,modcomment) WHERE id = ?", [$uid]);
                    SnatchedModel::update(['last_time', 'hnr'], ['uid='], [129600, 'no', $uid]);
                }
                break;
            case 'other':
                break;
            case 'VIP':
                $days = $row['value'];
                $vipuntil = ($USER->has('vipuntil') != null) ? $vipuntil = get_date_time(strtotime($USER->has('vipuntil')) + (30 * 86400)) : $vipuntil = get_date_time(gmtime() + (30 * 86400));
                $oldclass = ($USER->has('vipuntil') != null) ? $oldclass = $USER->has('oldclass') : $oldclass = $USER->has('class');
                UsersModel::update(['class','oldclass','vipuntil'], ['id='], [3, $oldclass, $vipuntil, $USER->has('id')]);
                break;
        }
        Redirect(Url('bonus'), Txt("SUCCESS"));
    }

    // Delete Id
    public static function delete($id, $sure = '')
    {
        if ($sure != 'sure') {
            Redirect(Url('bonus'), "Are You Sure <a href='".URLROOT."/bonus/delete/$id/sure'>Remove item id $id</a>");
        }
        DB::delete('bonus', ['id' => $id]);
        Redirect(Url('bonus'), Txt("DEL_COMPLETE"));
    }

    // Insert Bonus Or Update Bonus
    public static function insertOrUpdate()
    {
        if (!empty($_POST)) {
            $id = Input('id', 'int');
            $title = Input('title');
            $descr = Input('descr');
            $cost = Input('cost', 'int');
            $value = Input('value');
            $type = Input('type');
    
            if (empty($title) or empty($descr) or empty($type) or !is_numeric($cost)) {
                Redirect(Url('bonus'), Txt("MISSING_FORM_DATA"));
            }
    
            if (!$id) {
                DB::insert('bonus', ['title' => $title, 'descr' => $descr, 'cost' => $cost, 'value' => $value, 'type' => $type]);
            } else {
                DB::update('bonus', ['title','descr','cost','value','type'], ['id='], [$title, $descr, $cost, $value, $type, $id]);
            }
            Redirect(Url('bonus'), Txt("SUCCESS"));
        } else {
            Redirect(Url('bonus'), Txt("MISSING_FORM_DATA"));
        }
    }
}