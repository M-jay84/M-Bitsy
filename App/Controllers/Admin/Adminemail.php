<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Models\GroupsModel;
use App\Models\LogsModel;
use PDO;
use App\Libs\TTMail;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Mass Email Controller
*/
class Adminemail extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Mass Email Default Page
    public function index()
    {
    
        // Get Group Data
        $groups = (new GroupsModel)->getGroups();
        
        // Data For View
        $data = [
            'page_title' => LANG["MASS_EMAIL"],
            'V_groups' => $groups
        ];

        // Load View
        $this->view('email', $data, 'admin');
    }

    // Mass Email Form Submit
    public function sendemail()
    {
    
        // Check User Input
        $msg_log = "Sent to classes: ";
        @set_time_limit(0);
        $subject = Input('subject');
        $body = format_comment($_POST["body"]);

        // Check Correct Input
        if (!$subject || !$body) {
            showMessage(Url('adminemail'), LANG["MISSING_ID"]);
        }

        if (!@count($_POST["groups"])) {
            showMessage(Url('adminemail'), LANG["NOTHING_SELECTED"]);
        }

        // Get Group Data
        $ids = array_map("intval", $_POST["groups"]);
        $ids = implode(", ", $ids);
        $res_log = (new GroupsModel)->fetchlevel($ids);
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

        (new LogsModel)->write("<b><font color='Magenta'>A Mass E-Mail</font> was sent by (<font color='Navy'>".$this->current_user->username."</font>) $msg_log<b>");
        showMessage(Url('adminemail'), "<b><font color='#ff0000'>Mass mail sent....</font></b>");
    }

}