<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\LogsModel;

/*
* Rules Table Data
*/
class RulesModel
{
    // Get Rules
    public static function gettAll()
    {
        $stmt = DB::fetchAll('rules', '*', '', 'ORDER BY `id`');
        return $stmt;
    }

    // Admin Delete Rule
    public static function delete($id, $current_user_username)
    {
        DB::delete('rules', ['id' => $id]);
        LogsModel::write("Rules have been deleted by ".$current_user_username."");
        showMessage(('rule'), LANG["SUCCESS"]);
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
                DB::insert('rules', ['title'=>$title, 'text'=>$text, 'public'=>$public, 'class'=>$class]);
            } else {
                DB::update('rules', ['title','text','public', 'class'], ['id='], [$title, $text, $public, $class, $id]);
            }
            LogsModel::write("Rules have been changed by ".$current_user_username."");
            showMessage(('rule'), LANG["SUCCESS"]);
        } else {
            showMessage(URLROOT, LANG["MISSING_FORM_DATA"]);
        }
    }
}