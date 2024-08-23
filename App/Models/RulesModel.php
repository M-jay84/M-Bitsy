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
    public function gettAll()
    {
        $stmt = DB::fetchAll('rules', '*', '', 'ORDER BY `id`');
        return $stmt;
    }

    // Admin Delete Rule
    public function delete($id, $current_user_username)
    {
        DB::delete('rules', ['id' => $id]);
        (new LogsModel)->write("Rules have been deleted by ".$current_user_username."");
        showMessage(Url('rule'), LANG["SUCCESS"]);
    }

    // Admin Add/Update Rules
    public function insertOrUpdate($current_user_username)
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
            (new LogsModel)->write("Rules have been changed by ".$current_user_username."");
            showMessage(Url('rule'), LANG["SUCCESS"]);
        } else {
            showMessage(Url(), LANG["MISSING_FORM_DATA"]);
        }
    }
}