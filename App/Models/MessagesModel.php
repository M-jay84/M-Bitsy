<?php

namespace App\Models;

use PDO;
use App\Libs\DB;
use App\Libs\TTMail;
use App\Models\UsersModel;
use App\Models\GroupsModel;
use App\Models\LogsModel;

/*
* Messages Table Data
*/
class MessagesModel
{
    // Count Messages
    public static function countMsg()
    {
        $res = DB::run("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = ".$_SESSION['id']." AND `location` IN ('in','both')");
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE receiver=" . $_SESSION['id'] . " AND `location` IN ('in','both')");
        $inbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $_SESSION['id'] . " AND `location` IN ('in','both') AND `unread` = 'yes'");
        $unread = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " .$_SESSION['id'] . " AND `location` IN ('out','both')");
        $outbox = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) FROM messages WHERE `sender` = " . $_SESSION['id'] . " AND `location` = 'draft'");
        $draft = $res->fetchColumn();
        $res = DB::run("SELECT COUNT(*) AS count FROM messages WHERE `sender` = " . $_SESSION['id'] . " AND `location` = 'template'");
        $template = $res->fetchColumn();

        $arr = [
            'inbox' => $inbox,
            'unread' => $unread,
            'outbox' => $outbox,
            'draft' => $draft,
            'template' => $template,
        ];
        return $arr;
    }

    // Fetch Message Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('messages', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}
		
    // Fetch All Meesgaes
    public static function fetchMessages()
    {
        $stmt = DB::run("SELECT messages.*, users.avatar, users.username, users.class, `groups`.Color
                FROM messages 
                LEFT JOIN users ON users.id=messages.sender 
                LEFT JOIN `groups` ON groups.group_id=users.class
                WHERE receiver=" . $_SESSION['id'] . " and unread='yes' AND location IN ('in','both')"
                )->fetchAll();
		return $stmt;
	}
	
    // Templates Drop Down
    public static function echoTemplates()
    {
        $templates = "<option value=\"0\">---- " . Txt("NOTHING_SELECTED") . " ----</option>\n";
        $stmt = DB::fetchAll('messages', '*', ['sender=', 'location='], [$_SESSION['id'], 'template'], 'ORDER BY `subject`');
        foreach ($stmt as $arr) {
            $templates .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
        }
        return $templates;
    }

    // Add Message
    public static function insert($data)
    {
        DB::insert('messages', $data);
        // email notif
        $user = UsersModel::fetchRow('id, username, acceptpms, notifs, email', ['id='], [$data['receiver']]);
        if (strpos($user['notifs'], '[pm]') !== false) {
            $url = URLROOT;
            $body = "You have received a PM\n\nYou can use the URL below to view the message (you may have to login).\n\n$url/message/mailbox\n\n".CONFIG['SITENAME']."";
            $email = CONFIG['SITEEMAIL'];
            $TTMail = new TTMail();
            $TTMail->Send($user["email"], "You have received a PM", $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
        }
    }

    // Get Messages
    public static function msgPagination($type)
    {
        switch ($type) {
            case 'inbox':
                $where = "`receiver` = ".$_SESSION['id']." AND `location` IN ('in','both') ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/inbox&");
                break;
            case 'outbox':
                $where = "`sender` = ".$_SESSION['id']." AND `location` IN ('out','both') ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/outbox&");
                break;
            case 'templates':
                $where = "`sender` = ".$_SESSION['id']." AND `location` = 'template' ORDER BY added DESC";
                $count= DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/templates&");
                break;
            case 'draft':
                $where = "`sender` = ".$_SESSION['id']." AND `location` = 'draft' ORDER BY added DESC";
                $count = DB::run("SELECT COUNT(*) FROM messages WHERE $where")->fetchColumn();
                list($pagerbuttons, $limit) = pagination(25, $count, URLROOT."/message/mailbox/draft&");
                break;
            }
        $messages = DB::run("SELECT * FROM messages WHERE $where $limit")->fetchAll();

        $arr = ['pager' => $pagerbuttons, 'messages' => $messages];

        return $arr;
    }

    // Add Message Type
    public static function insertByType()
    {
        // Check User Input
        $receiver = Input('receiver');
        $subject = Input('subject');
        $body = Input('body');
        $history = Input('history', 'int');

        // Check Correct Input
        if (strlen($body) < 5 || strlen($subject) < 3) {
            Redirect(Url('message/mailbox/inbox'), Txt('SMALL_INPUT'));
        }

        // Get User Id
        if (!is_numeric($receiver)) {
            $receiver = UsersModel::fetchColumn('id', ['username ='], [$receiver]);
        }
        
        if ($receiver == 0 && $_REQUEST['Update'] == 'create') {
            Redirect(Url('message/mailbox/inbox'), Txt('MISSING_ID'));
        }
        

        switch ($_REQUEST['Update']) {
            case 'create':
                if (isset($_POST['save'])) {
                    MessagesModel::insert(['sender'=>$_SESSION['id'], 'receiver'=>$receiver, 'added'=>get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'both', 'history' => $history]);
                } else {
                    MessagesModel::insert(['sender'=>$_SESSION['id'], 'receiver'=>$receiver, 'added'=>get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'yes', 'location'=>'in', 'history' => $history]);
                }
                Redirect(Url('message/mailbox/outbox'), Txt('MESSAGES_SENT'));
                break;
            case 'draft':
                MessagesModel::insert(['sender'=>$_SESSION['id'], 'receiver'=>$receiver, 'added'=>get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'draft']);
                Redirect(Url('message/mailbox/draft'), Txt('SAVED_DRAFT'));
                break;
            case 'template':
                MessagesModel::insert(['sender'=>$_SESSION['id'], 'receiver'=>$receiver, 'added'=>get_date_time(), 'subject'=>$subject, 'msg'=>$body, 'unread'=>'no', 'location'=>'template']);
                Redirect(Url('message/mailbox/templates'), Txt('SAVED_TEMPLATE'));
                break;
        }
    }

    // Delete Message
    public static function delete($type)
    {
        $ids = multiIds('del');

        if ($_POST["read"]) {
            DB::updateWhereIn('meesages', ['unread' => 'no'], $ids);
        } else {
            if ($type == 'inbox') {
                DB::run("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = ".$_SESSION['id']." AND `id` IN ($ids)");
                DB::run("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = ".$_SESSION['id']." AND `id` IN ($ids)");
            } elseif ($type == 'outbox') {
                DB::run("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = ".$_SESSION['id']." AND `id` IN ($ids)");
                DB::run("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = ".$_SESSION['id']." AND `id` IN ($ids)");
            } elseif ($type == 'templates') {
                DB::deleteByIds(
                    'messages', 
                    ['sender =','location='], 
                    $ids, 
                    'id', 
                    [$_SESSION['id'], 'template']
                );
            } elseif ($type == 'draft') {
                DB::deleteByIds(
                    'messages', 
                    ['sender =','location='], 
                    $ids, 
                    'id', 
                    [$_SESSION['id'], 'draft']
                );
            }
        }
        Redirect(Url("message/mailbox/$type"), Txt("SUCCESS"));
    }

    // Get Message Id
    public static function getById($id)
    {
        $arr = DB::fetchRow('messages', '*', ['id='], [$id]);
        
        if ($arr["history"] != 0) {
            $history = DB::fetchAll('messages', '*', ['history='], [$arr["history"]])->fetchAll();
        }

        // Check User
        if ($arr["sender"] != $_SESSION['id'] && $arr["receiver"] != $_SESSION['id']) {
            Redirect(Url(), Txt('NO_PERMISSION'));
        }
        
        // Mark Read
        if ($arr["unread"] == "yes" && $arr["receiver"] == $_SESSION['id']) {
            DB::update('messages', ['unread'], ['id=', 'receiver='], ['no', $id, $_SESSION['id']]);
        }

		$arr["replyto"] = (($_SESSION['id']== $arr["receiver"])) ? $arr["sender"] : $arr["receiver"];
        $arr['previous'] = $history;

        return $arr;
    }

    // Update Message
    public static function update($id)
    {
        // Get Message
        $row = DB::fetchRow('messages', '*', ['id='], [$id]);
        
        if ($row["sender"] != $_SESSION['id'] && $row["receiver"] != $_SESSION['id']) {
            Redirect(Url(), Txt('NO_PERMISSION'));
        }
        if (!$row) {
            Redirect(Url('message/mailbox/inbox'), Txt("MISSING_ID"));
        }
        
        // Submit Form
        if (!empty($_POST)) {
            $msg = isset($_POST['msg']) ? $_POST['msg'] : '';
            // Update Message
            DB::update('messages', ['msg'], ['id='], [$msg,$id]);
            Redirect(Url('message/mailbox/inbox'), Txt("UPDATE_COMPLETE"));
        }
        Redirect(Url('message/mailbox/inbox'), Txt("EDIT_FAILED"));
    }

    // Admin Delete Messages
    public static function deleteByIds()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('messages', 'id', $ids);
        Redirect(Url('adminmessage'), Txt("DEL_COMPLETE"));
    }

    // Admin Send Multiple Messages
    public static function multiPM()
    {
        $sender_id = Input('sender_id');
        $receiver = Input('receiver');
        $id = Input('id');
        $msg = Input('msg');
        $subject = Input('sebject');
        // Check Correct Input
        if (!$msg) {
            Redirect(Url('adminmessage/masspm'), Txt("MISSING_FORM_DATA"));
        }

        $updateset = array_map("intval", $_POST['classes']);
        $query = DB::run("SELECT id FROM users WHERE class IN (" . implode(",", $updateset) . ")");// AND enabled = 'yes' AND status = 'confirmed'
        while ($dat = $query->fetch(PDO::FETCH_ASSOC)) {
            DB::insert('messages', ['sender' => $sender_id, 'receiver' => $dat['id'], 'added' => get_date_time(), 'msg' => $msg, 'subject' => $subject]);
        }
        
        LogsModel::write("A Mass PM was sent by " . $_SESSION['id'] . "");
        Redirect(Url('adminmessage/masspm'), Txt("SUCCESS"));
    }

    // Admin Get All Messages
    public static function getAll()
    {
        // Pagination
        $count = DB::run("SELECT COUNT(*) FROM messages WHERE location in ('in', 'both')")->fetchColumn();
        list($pager, $limit) = pagination(40, $count, "/adminmessage?;");
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