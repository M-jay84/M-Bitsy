<?php

namespace App\Controllers\Admin;

use App\Models\GroupsModel;
use App\Models\LogsModel;
use PDO;
use App\Libs\TTMail;
use App\Libs\User;
use App\Models\UsersModel;

/*
* Mass Email Controller
*/
class Adminemail 
{
    // Mass Email Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Get Group Data
        $groups = GroupsModel::getGroups();
        
        // Init Data
        $data = [
            'V_title' => Txt("MASS_EMAIL"),
            'V_groups' => $groups,
            'USER' => $USER
        ];

        // Load View
        template('email', $data, 'admin');
    }

    // Mass Email Form Submit
    public function sendemail()
    {
        $USER = new User(_MODERATOR, 2);
        // Check User Input
        $msg_log = "Sent to classes: ";
        @set_time_limit(0);
        $subject = Input('subject');
        $body = format_comment($_POST["body"]);

        // Check Correct Input
        if (!$subject || !$body) {
            Redirect(Url('adminemail'), Txt("MISSING_ID"));
        }

        if (!@count($_POST["groups"])) {
            Redirect(Url('adminemail'), Txt("NOTHING_SELECTED"));
        }

        // Get Group Data
        $ids = array_map("intval", $_POST["groups"]);
        $ids = implode(", ", $ids);
        $res_log = GroupsModel::fetchlevel($ids);
        foreach ($res_log as $row_log) {
            $msg_log .= $row_log["level"] . ", ";
        }
        
        // Send Email
        $res = UsersModel::getEmail($ids);
        $siteemail = CONFIG['SITEEMAIL'];
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $TTMail = new TTMail();
            $TTMail->Send($row["email"], $subject, $body, "Content-type: text/html; charset=utf-8", "-f$siteemail");
        }

        LogsModel::write("<b><font color='Magenta'>A Mass E-Mail</font> was sent by (<font color='Navy'>".$USER->has('username')."</font>) $msg_log<b>");
        Redirect(Url('adminemail'), "<b><font color='#ff0000'>Mass mail sent....</font></b>");
    }

}