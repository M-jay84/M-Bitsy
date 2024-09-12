<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use PDO;
use App\Libs\TTMail;
use App\Libs\Controller;
use App\Libs\DB;
use App\Libs\Validate;
use App\Models\UsersModel;

/*
* Inactive User Controller
*/
class Admininactive extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Inactive Users Default Page
    public function index()
    {
        // Inactivite Days
        $cday = Validate::Input('cday', 'int') ?? 30;
        $dt = date("Y-m-d H:i:s", CONFIG['TT_TIME'] - ($cday * 86400));
        
        // Get Users Data
        $users = DB::newSelect('users', 'id,username,class,email,uploaded,downloaded,last_access,ip,added', 
                                ['last_access<','status=','enabled='] , [$dt, 'confirmed', 'yes'], "ORDER BY last_access DESC");
        $count = $users->rowCount();

        // Data For View
        $data = [
            'page_title' => LANG["INACTIVE"],
            'V_cday' => $cday,
            'V_users' => $users,
            'V_count' => $count
        ];
        
        // Load View
        $this->view('admin-inactive', $data, 'admin');
    }

    // Inactive Users Form Submit
    public function submit()
    {
        $sitename = CONFIG['SITENAME'];
        $siteurl = URLROOT;
        
        if (!empty($_POST)) { // todo full file
            // Check User Input
            $action = Validate::Input('action');
            $cday = Validate::Input('cday', 'int');
            $days = 30; // Number of days of inactivite

            // Check Correct Input
            if (!is_numeric($cday)) {
                $this->showMessage('admininactive', LANG["ACTION_FAILED"]);
            }

            if (empty($_POST["userid"]) && (($action == "deluser") || ($action == "mail") || ($action == "disable"))) {
                $this->showMessage('admininactive', ("NOTHING_SELECTED"));
            }

            if ($action == "cday" && ($cday > $days)) {
                $days = $cday;
            }

            // Delete
            if ($action == "deluser" && (!empty($_POST["userid"]))) {
                $ids = implode(", ", $_POST['userid']);
                DB::deleteByIds('users', 'id', $ids);
                $this->showMessage('admininactive', LANG["SUCCESS"]);
            }

            // Disable
            if ($action == "disable" && (!empty($_POST["userid"]))) {
                $res = UsersModel::selectWhereIn();
                while ($arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $id = 0 + $arr["id"];
                    $cname = $this->current_user->username;
                    $modcomment = $arr["modcomment"];
                    $modcomment = date("Y-m-d") . " - Disabled for inactivity by $cname.n" . $modcomment;
                    DB::update('users', ['modcomment', 'enabled'], ['id='], [$modcomment,'no',$id]);
                }
                $this->showMessage('admininactive', LANG["UPDATE_COMPLETE"]);
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
                    $body = file_get_contents(VIEW_PATH . "/user/email/changeemail.php");
                    $body = str_replace("%username%", $username, $body);
                    $body = str_replace("%sitename%", $sitename, $body);
                    $body = str_replace("%siteurl%", $siteurl, $body);

                    $body = str_replace("%added%", $$added, $body);
                    $body = str_replace("%last_access%", $last_access, $body);
                    $TTMail = new TTMail();
                    $mail = $TTMail->Send($email, $subject, $body, "From: " . CONFIG['SITEEMAIL'] . "", "-f" . CONFIG['SITEEMAIL'] . "");
                }

                if ($mail) {
                    $this->showMessage('admininactive', "Messages sent.");
                } else {
                    $this->showMessage('admininactive',  "Try again.");
                }

            }
        }

    }

}