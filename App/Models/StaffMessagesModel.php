<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\MessagesModel;

/*
* Staff Messages Table Data
*/
class StaffMessagesModel
{
    // Admin Answer Message
    public function update($current_userid)
    {
        $id = Input('id', 'int');
        $response = Input('response');
        $sender = Input('sender');

        // Check Correct Input
        if (!is_numeric($id) || empty($response)) {
            showMessage(Url('admincontact'), LANG["MISSING_DATA"]);
        }
        
        // Take Answer
        if ($sender) {
            MessagesModel::insert(['sender'=>$current_userid, 'receiver'=>$sender, 'added'=>get_date_time(), 'subject'=>'Reply to recent staff message', 'msg'=>$response, 'unread'=>'yes', 'location'=>'in']);
        }
        // Take Answer
        DB::update('staffmessages', ['answered','answeredby','answer'], ['id='], [1, $current_userid, $response, $id]);
        showMessage(Url('admincontact'), "Staff Message $id has been answered.");
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
        showMessage(Url('admincontact'), "Staff Message has been deleted.");
    }

    // Add Message
    public function insert($current_userid = 0)
    {
        // Check Input Else Return To Form
        if (csrf_check()) {
            captcha_response($_POST['g-recaptcha-response']);
            $msg = Input('msg');
            $subject = Input('subject');

            // Check Correct Input
            if (!$msg || !$subject) {
                showMessage(Url(), LANG["MISSING_FORM_DATA"]);
            }
            if (strlen($msg) < 10 || strlen($subject) < 5) {
                showMessage(Url(), LANG["SMALL_INPUT"]);
            }

            DB::insert('staffmessages', ['sender' => $current_userid, 'added' => get_date_time(), 'msg' => $msg, 'subject' => $subject]);
            showMessage(Url(), LANG["SUCCESS"]);

        } else {
            Url(Url('contact'), true);
        }
    }

}