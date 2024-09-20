<?php

namespace App\Models;

use PDO;
use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Validate;
use App\Models\GroupsModel;
use App\Models\LogsModel;

/*
* Messages Table Data
*/
class MessagesModel
{
    // Add Message
    public static function insert($data)
    {
        DB::insert('messages', $data);
        // email notif
        $user = DB::fetchRow('users', 'id, username, acceptpms, notifs, email', ['id='], [$data['receiver']]);
        if (strpos($user['notifs'], '[pm]') !== false) {
            $url = URLROOT;
            $body = "You have received a PM\n\nYou can use the URL below to view the message (you may have to login).\n\n$url/message/mailbox\n\n".CONFIG['SITENAME']."";
            $TTMail = new TTMail();
            $TTMail->Send($user["email"], "You have received a PM", $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
        }
    }

    // Get Messages
    public static function msgPagination($type, $id)
    {
        switch ($type) {
            case 'inbox':
                $where = "`receiver` = ".$id." AND `location` IN ('in','both') ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/inbox&");
                break;
            case 'outbox':
                $where = "`sender` = ".$id." AND `location` IN ('out','both') ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/outbox&");
                break;
            case 'templates':
                $where = "`sender` = ".$id." AND `location` = 'template' ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/templates&");
                break;
            case 'draft':
                $where = "`sender` = ".$id." AND `location` = 'draft' ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/draft&");
                break;
            }
        $messages = DB::run("SELECT * FROM messages WHERE $where $limit")->fetchAll();

        $arr = ['pager' => $pagerbuttons, 'messages' => $messages];

        return $arr;
    }

    // Add Message Type
    public static function insertByType($id)
    {
        // Check User Input
        $receiver = Validate::Input('receiver');
        $subject = Validate::Input('subject');
        $body = Validate::Input('body');
        $history = Validate::Input('history', 'int');

        // Check Correct Input
        if (strlen($body) < 5 || strlen($subject) < 3) {
            showMessage('message/mailbox/inbox', LANG['SMALL_INPUT']);
        }

        // Get User Id
        if (!is_numeric($receiver)) {
            $receiver = DB::fetchColumn('users', 'id', ['username ='], [$receiver]);
        }
        
        if ($receiver == 0 && $_REQUEST['Update'] == 'create') {
            showMessage('message/mailbox/inbox', LANG['MISSING_ID']);
        }
        

        switch ($_REQUEST['Update']) {
            case 'create':
                if (isset($_POST['save'])) {
                    MessagesModel::insert(['sender'=>$id, 'receiver'=>$receiver, 'added'=>CONFIG['TT_DATE'], 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'both', 'history' => $history]);
                } else {
                    MessagesModel::insert(['sender'=>$id, 'receiver'=>$receiver, 'added'=>CONFIG['TT_DATE'], 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'in', 'history' => $history]);
                }
                showMessage('message/mailbox/outbox', LANG['MESSAGES_SENT']);
                break;
            case 'draft':
                MessagesModel::insert(['sender'=>$id, 'receiver'=>$receiver, 'added'=>CONFIG['TT_DATE'], 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'draft']);
                showMessage('message/mailbox/draft', LANG['SAVED_DRAFT']);
                break;
            case 'template':
                MessagesModel::insert(['sender'=>$id, 'receiver'=>$receiver, 'added'=>CONFIG['TT_DATE'], 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'template']);
                showMessage('message/mailbox/templates', LANG['SAVED_TEMPLATE']);
                break;
        }
    }

    // Delete Message
    public static function delete($type, $current_user)
    {
        $ids = Validate::multiIds('del');

        if ($type == 0 && $current_user->isStaff) {
            die('todo');
        } elseif ($_POST["read"]) {
            DB::updateWhereIn('messages', ['unread' => 'no'], $ids);
        } elseif ($type == 'inbox') {
                DB::run("DELETE FROM messages WHERE `location` = ? AND `receiver` = ? AND `id` IN (?)", ['in', $current_user->id, $ids]);
                DB::run("UPDATE messages SET `location` = ? WHERE `location` = ? AND `receiver` = ? AND `id` IN (?)", ['out', 'both', $current_user->id, $ids]);
        } elseif ($type == 'outbox') {
                DB::run("UPDATE messages SET `location` =  WHERE `location` = ? AND `sender` = ? AND `id` IN (?)", ['in', 'both', $current_user->id, $ids]);
                DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = ? AND `id` IN (?)", [$current_user->id, $ids]);
        }
        showMessage(("message/mailbox/$type"), LANG["SUCCESS"]);
    }

    // Get Message Id
    public static function getById($id, $userid)
    {
        $arr = DB::fetchRow('messages', '*', ['id='], [$id]);
        
        if ($arr["history"] != 0) {
            $history = DB::fetchAll('messages', '*', ['history='], [$arr["history"]])->fetchAll();
        }

        // Check User
        if ($arr["sender"] != $userid && $arr["receiver"] != $userid) {
            showMessage(URLROOT, LANG['NO_PERMISSION']);
        }
        
        // Mark Read
        if ($arr["unread"] == "yes" && $arr["receiver"] == $userid) {
            DB::update('messages', ['unread'], ['id=', 'receiver='], ['no', $id, $userid]);
        }

		$arr["replyto"] = (($userid == $arr["receiver"])) ? $arr["sender"] : $arr["receiver"];
        $arr['previous'] = $history;

        return $arr;
    }

    // Update Message
    public static function update($id, $userid)
    {
        // Get Message
        $row = DB::fetchRow('messages', '*', ['id='], [$id]);
        
        if ($row["sender"] != $userid && $row["receiver"] != $userid) {
            showMessage(URLROOT, LANG['NO_PERMISSION']);
        }
        if (!$row) {
            showMessage('message/mailbox/inbox', LANG["MISSING_ID"]);
        }
        
        // Submit Form
        if (!empty($_POST)) {
            $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
            // Update Message
            DB::update('messages', ['msg'], ['id='], [$msg,$id]);
            showMessage('message/mailbox/inbox', LANG["UPDATE_COMPLETE"]);
        }
        showMessage('message/mailbox/inbox', LANG["EDIT_FAILED"]);
    }

    // Admin Delete Messages
    public static function deleteByIds()
    {
        $ids = Validate::multiIds('ids');
        DB::deleteByIds('messages', 'id', $ids);
        showMessage('adminmessage', LANG["DEL_COMPLETE"]);
    }

    // Admin Send Multiple Messages
    public static function multiPM($userid)
    {
        $sender_id = Validate::Input('sender_id');
        $receiver = Validate::Input('receiver');
        $id = Validate::Input('id');
        $msg = Validate::Input('msg');
        $subject = Validate::Input('sebject');
        // Check Correct Input
        if (!$msg) {
            showMessage('adminmessage/masspm', LANG["MISSING_FORM_DATA"]);
        }

        
        $updateset = array_map("intval", $_POST['classes']);
        $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ")");// AND enabled = 'yes' AND status = 'confirmed'
        while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
            DB::insert('messages', ['sender' => $sender_id, 'receiver' => $dat['id'], 'added' => CONFIG['TT_DATE'], 'msg' => $msg, 'subject' => $subject]);
        }
        
        LogsModel::write("A Mass PM was sent by " . $userid . "");
        showMessage('adminmessage/masspm', LANG["SUCCESS"]);
    }

    // Admin Get All Messages
    public static function getAll()
    {
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetchColumn();
        list($pager, $limit) = pagination(40, $count, '/adminmessage&');
        $stmt = DB::run("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit")->fetchAll();

        // Get Groups
        $groups = GroupsModel::fetchAll('group_id, level', '');
        $array = [
            'res' => $stmt,
            'pager' => $pager,
            'groups' => $groups
        ];
        return $array;
    }

}