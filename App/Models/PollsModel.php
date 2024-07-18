<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\PollsAnswerModel;

/*
* Polls Table Data
*/
class PollsModel
{
    // Fetch Poll Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('polls', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Fetch Poll Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('polls', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Admin Fetch Poll Row
    public static function getAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchAll('polls', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Admin Add/Edit Poll
    public static function insertOrUpdate()
    {
        $id = Input('id', 'int');

        $data = [
            'question' => Input('question'),
            'option0' => Input('option0'),
            'option1' => Input('option1'),
            'option2' => Input('option2'),
            'option3' => Input('option3'),
            'option4' => Input('option4'),
            'option5' => Input('option5'),
            'option6' => Input('option6'),
            'option7' => Input('option7'),
            'option8' => Input('option8'),
            'option9' => Input('option9'),
            'option10' => Input('option10'),
            'option11' => Input('option11'),
            'sort' => Input('sort', 'int'),
            'added' => get_date_time()
        ];

        // Check Correct Input
        if (!$data['question'] || !$data['option0'] || !$data['option1']) {
            Redirect(Url('adminpoll'), Txt("MISSING_FORM_DATA") . "!");
        }

        if ($id) {
            DB::updateset("polls", $data, ['id'=>$id]);
        } else {
            DB::insert("polls", $data);
        }
        
        Redirect(Url('adminpoll'), Txt("ACTION_COMPLETE"));
    }

    // Admin Delete Poll
    public static function delete($id)
    {
        DB::delete('polls', ['id'=>$id]);
        PollsanswerModel::delete($id);
        Redirect(Url('adminpoll'), Txt("ACTION_COMPLETE"));
    }

}