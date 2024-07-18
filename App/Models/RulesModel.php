<?php

namespace App\Models;

use App\Libs\DB;
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
    public static function delete($id)
    {
        DB::delete('rules', ['id' => $id]);
        LogsModel::write("Rules have been deleted by ".$_SESSION['username']."");
        Redirect(Url('rule'), Txt("SUCCESS"));
    }

    // Admin Add/Update Rules
    public static function insertOrUpdate()
    {
        if (!empty($_POST)) {
            $id = Input('id', 'int');
            $title = Input('title');
            $text = Input('text');
            $public = Input('public');
            $class = Input('classs');
            if ($id == 0) {
                DB::insert('rules', ['title'=>$title, 'text'=>$text, 'public'=>$public, 'class'=>$class]);
            } else {
                DB::update('rules', ['title','text','public', 'class'], ['id='], [$title, $text, $public, $class, $id]);
            }
            LogsModel::write("Rules have been changed by ".$_SESSION['username']."");
            Redirect(Url('rule'), Txt("SUCCESS"));
        } else {
            Redirect(Url(), Txt("MISSING_FORM_DATA"));
        }
    }
}