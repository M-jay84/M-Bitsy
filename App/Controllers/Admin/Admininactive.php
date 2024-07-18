<?php

namespace App\Controllers\Admin;

use PDO;
use App\Libs\TTMail;
use App\Libs\User;
use App\Models\UsersModel;

/*
* Inactive User Controller
*/
class Admininactive 
{
    // Inactive Users Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Inactivite Days
        $cday = Input('cday', 'int') ?? 30;
        $dt = get_date_time(gmtime() - ($cday * 86400));
        
        // Get Users Data
        $users = UsersModel::fetchAll('id,username,class,email,uploaded,downloaded,last_access,ip,added', 
                                ['last_access<','status=','enabled='] , [$dt, 'confirmed', 'yes'], "ORDER BY last_access DESC");
        $count = $users->rowCount();

        // Init Data
        $data = [
            'V_title' => Txt("INACTIVE"),
            'V_cday' => $cday,
            'V_users' => $users,
            'V_count' => $count,
            'USER' => $USER
        ];
        
        // Load View
        template('inactive', $data, 'admin');
    }

    // Inactive Users Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        $sitename = CONFIG['SITENAME'];
        $siteurl = URLROOT;
        
        if (!empty($_POST)) { // todo full file
            // Check User Input
            $action = Input('action');
            $cday = Input('cday', 'int');
            $days = 30; // Number of days of inactivite

            // Check Correct Input
            if (!is_numeric($cday)) {
                Redirect(Url('admininactive'), Txt("ACTION_FAILED"));
            }

            if (empty($_POST["userid"]) && (($action == "deluser") || ($action == "mail") || ($action == "disable"))) {
                Redirect(Url('admininactive'), ("NOTHING_SELECTED"));
            }

            if ($action == "cday" && ($cday > $days)) {
                $days = $cday;
            }

            // Delete
            if ($action == "deluser" && (!empty($_POST["userid"]))) {
                $ids = implode(", ", $_POST['userid']);
                UsersModel::deleteByIds($ids);
                Redirect(Url('admininactive'), Txt("SUCCESS"));
            }

            // Disable
            if ($action == "disable" && (!empty($_POST["userid"]))) {
                $res = UsersModel::selectWhereIn();
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = 0 + $arr["id"];
                    $cname = $USER->has('username');
                    $modcomment = $arr["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Disabled for inactivity by $cname.n" . $modcomment;
                    UsersModel::update(['modcomment', 'enabled'], ['id='], [$modcomment,'no',$id]);
                }
                Redirect(Url('admininactive'), Txt("UPDATE_COMPLETE"));
            }

            // Email
            if ($action == "mail" && (!empty($_POST["userid"]))) {
                $res = UsersModel::selectWhereIn(true);
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = $arr["id"];
                    $username = htmlspecialchars($arr["username"]);
                    $email = htmlspecialchars($arr["email"]);
                    $added = $arr["added"];
                    $last_access = $arr["last_access"];
                    $subject = "Warning for inactive account on $sitename";

                    // Send Email
                    $body = file_get_contents(EMAILDIR . "/changeemail.php");
                    $body = str_replace("%username%", $username, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $body = str_replace("%siteurl%", $siteurl, $body);

                    $body = str_replace("%added%", $$added, $body);
                    $body = str_replace("%last_access%", $last_access, $body);
                    $TTMail = new TTMail();
                    $mail = $TTMail->Send($email, $subject, $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
                }

                if ($mail) {
                    Redirect(Url('admininactive'), "Messages sent.");
                } else {
                    Redirect(Url('admininactive'),  "Try again.");
                }

            }
        }

    }

}