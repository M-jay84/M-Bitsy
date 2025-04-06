<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\CommentsLogic;

/*
* News Table Data
*/
class NewsLogic
{
    // Admin Add/Edit News
    public static function insertOrUpdate()
    {
        // Check User Input
        $body = Validate::Input('body');
        $title = Validate::Input('title');
        $added = Validate::Input('added');
        $id = Validate::Input('id');
       
        // Check Correct Input
        if (!$body || !$title) {
            showMessage('adminnews', LANG["MISSING_FORM_DATA"]);
        }
        
        if (!$added) {
            $added = CONFIG['TT_DATE'];
        }

        if (!$id) {
            $news_query = "INSERT INTO news (userid,added,body,title) VALUES (?,?,?,?)";
            DB::run($news_query, [$id, CONFIG['TT_DATE'], $body, $title]);
        } else {
            $news_query = "UPDATE news SET body = ? AND title = ? WHERE id=?";
            DB::run($news_query, [$body, $title, $id]);
        }
        showMessage('adminnews', LANG["ACTION_COMPLETE"]);
    }

    // Admin Delete Catagorie
    public static function getAll()
    {
        $news_query = "SELECT * FROM `news` ORDER BY added DESC";
        $news = DB::run($news_query);
        return $news;
    }

    // Admin Delete News
    public static function delete($id, $userid)
    {
        $news_query = "DELETE FROM `news` WHERE id' = ?";
        DB::run($news_query, [$id]);
        CommentsLogic::delete($id, $userid, 'news');
        showMessage('adminnews', LANG["DEL_COMPLETE"]);
    }

}