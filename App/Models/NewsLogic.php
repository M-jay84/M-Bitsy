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
    // Fetch All News
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('news', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch News Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('news', $columns, $where, $values, $other);
		return $stmt;
	}

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
            DB::insert('news', ['userid'=>$id, 'added'=>CONFIG['TT_DATE'], 'body'=>$body, 'title'=>$title]);
        } else {
            DB::update('news', ['body', 'title'], ['id='], [$body, $title, $id]);
        }
        showMessage('adminnews', LANG["ACTION_COMPLETE"]);
    }

    // Admin Delete News
    public static function delete($id, $userid)
    {
        DB::delete('news', ['id'=>$id]);
        CommentsLogic::delete($id, $userid, 'news');
        showMessage('adminnews', LANG["DEL_COMPLETE"]);
    }

}