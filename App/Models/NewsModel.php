<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\CommentsModel;

/*
* News Table Data
*/
class NewsModel
{
    // Get News
    public static function get() {
        $res = DB::run("SELECT news.id, news.title, news.added, news.body, users.username 
        FROM news 
        LEFT JOIN users 
        ON news.userid = users.id 
        ORDER BY added 
        DESC LIMIT 10");
        return $res;
    }
	
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
        $body = Input('body');
        $title = Input('title');
        $added = Input('added');
        $id = Input('id');
       
        // Check Correct Input
        if (!$body || !$title) {
            Redirect(Url('adminnews'), Txt("MISSING_FORM_DATA"));
        }
        
        if (!$added) {
            $added = get_date_time();
        }

        if (!$id) {
            DB::insert('news', ['userid'=>$_SESSION['id'], 'added'=>get_date_time(), 'body'=>$body, 'title'=>$title]);
        } else {
            DB::update('news', ['body', 'title'], ['id='], [$body, $title, $id]);
        }
        Redirect(Url('adminnews'), Txt("ACTION_COMPLETE"));
    }

    // Admin Delete News
    public static function delete($id)
    {
        DB::delete('news', ['id'=>$id]);
        CommentsModel::delete($id, 'news');
        Redirect(Url('adminnews'), Txt("DEL_COMPLETE"));
    }

}