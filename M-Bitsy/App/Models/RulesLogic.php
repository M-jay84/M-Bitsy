<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsLogic;

/*
* Rules Table Data
*/
class RulesLogic
{
    // Get Rules
    public static function gettAll()
    {
        $rules_query = "SELECT * FROM `rules` ORDER BY id";
        $stmt = DB::run($rules_query);
        return $stmt;
    }

    // Admin Delete Rule
    public static function delete($id, $current_user_username)
    {
        $rules_query = "DELETE FROM rules WHERE id' = ?";
        DB::run($rules_query, [$id]);
        LogsLogic::write("Rules have been deleted by ".$current_user_username."");
        showMessage('rule', LANG["SUCCESS"]);
    }

    // Admin Add/Update Rules
    public static function insertOrUpdate($current_user_username)
    {
        if (!empty($_POST)) {
            $id = Validate::Input('id', 'int');
            $title = Validate::Input('title');
            $text = Validate::Input('text');
            $public = Validate::Input('public');
            $class = Validate::Input('classs');
            if ($id == 0) {
                $rules_query = "INSERT INTO `rules` (title, text, public, class) VALUES (?,?,?,?)";
                DB::run($rules_query, [$title, $text, $public, $class]);
            } else {
                $rules_query = "UPDATE rules SET title = ? AND text = ? AND public = ? AND class = ? WHERE id=?";
                DB::run($rules_query, [$title, $text, $public, $class, $id]);
            }
            LogsLogic::write("Rules have been changed by ".$current_user_username."");
            showMessage('rule', LANG["SUCCESS"]);
        } else {
            showMessage(URLROOT, LANG["MISSING_FORM_DATA"]);
        }
    }
}