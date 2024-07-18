<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\MessagesModel;

/*
* Staff Messages Table Data
*/
class StaffMessagesModel
{
    // Fetch Message Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('staffmessages', $columns, $where, $values, $other)->fetchColumn();
        return  $stmt;
    }
	
    // Admin Answer Message
    public static function update()
    {
        $id = Input('id', 'int');
        $response = Input('response');
        $sender = Input('sender');

        // Check Correct Input
        if (!is_numeric($id) || empty($response)) {
            Redirect(Url('admincontact'), Txt("MISSING_DATA"));
        }
        
        // Take Answer
        if ($sender) {
            MessagesModel::insert(['sender'=>$_SESSION['id'], 'receiver'=>$sender, 'added'=>get_date_time(), 'subject'=>'Reply to recent staff message', 'msg'=>$response, 'unread'=>'yes', 'location'=>'in']);
        }
        // Take Answer
        DB::update('staffmessages', ['answered','answeredby','answer'], ['id='], [1, $_SESSION['id'], $response, $id]);
        Redirect(Url('admincontact'), "Staff Message $id has been answered.");
    }

    // Admin Get Messages
    public static function select()
    {
        $stmt = DB::fetchAll('staffmessages', '*', '', 'ORDER BY `id` desc');
        return $stmt;
    }

    // Admin Delete Messages
    public static function delete()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('staffmessages', 'id', $ids);
        Redirect(Url('admincontact'), "Staff Message has been deleted.");
    }

    // Add Message
    public static function insert()
    {
        // Check Input Else Return To Form
        if (csrf_check()) {
            captcha_response($_POST['g-recaptcha-response']);
            $msg = Input('msg');
            $subject = Input('subject');

            // Check Correct Input
            if (!$msg || !$subject) {
                Redirect(Url(), Txt("MISSING_FORM_DATA"));
            }
            if (strlen($msg) < 10 || strlen($subject) < 5) {
                Redirect(Url(), Txt("SMALL_INPUT"));
            }

            $userid = $_SESSION['id'] > 0 ? $_SESSION['id'] : 0;
            DB::insert('staffmessages', ['sender' => $userid, 'added' => get_date_time(), 'msg' => $msg, 'subject' => $subject]);
            Redirect(Url(), Txt("SUCCESS"));

        } else {
            Redirect(Url('contact'));
        }
    }

}