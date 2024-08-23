<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\CommentsModel;

/*
* News Table Data
*/
class NewsModel
{
    // Fetch All News
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('news', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch News Row
    public function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('news', $columns, $where, $values, $other);
		return $stmt;
	}

    // Admin Add/Edit News
    public function insertOrUpdate($data = [])
    {
        // Check User Input
        $body = Input('body');
        $title = Input('title');
        $added = Input('added');
        $id = Input('id');
       
        // Check Correct Input
        if (!$body || !$title) {
            showMessage(Url('adminnews'), LANG["MISSING_FORM_DATA"]);
        }
        
        if (!$added) {
            $added = get_date_time();
        }

        if (!$id) {
            DB::insert('news', ['userid'=>$data['current_user']->id, 'added'=>get_date_time(), 'body'=>$body, 'title'=>$title]);
        } else {
            DB::update('news', ['body', 'title'], ['id='], [$body, $title, $id]);
        }
        showMessage(Url('adminnews'), LANG["ACTION_COMPLETE"]);
    }

    // Admin Delete News
    public function delete($id, $userid)
    {
        DB::delete('news', ['id'=>$id]);
        (new CommentsModel)->delete($id, $userid, 'news');
        showMessage(Url('adminnews'), LANG["DEL_COMPLETE"]);
    }

}