<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\TTMail;
use App\Libs\Validate;

/*
* Invites Table Data
*/
class InvitesModel
{
    // Admin Fetch All Pending Users
    public static function sendInvite($current_user)
    {
        // Check User Input
        $email = Validate::Input('email', 'email');

        // Check Email Not Banned
        $message = BansModel::checkEmail($email);
        if ($message) {
            showMessage(URLROOT, $message);
        }

        // Set Invite
        $secret = mksecret();
        $username = "invite_" . mksecret(20);
        $id = DB::insert('users', [
            'username'=>$username, 'secret'=>$secret, 'email'=>$email, 'status'=>'pending',
            'invited_by'=>$current_user->id, 'added'=>CONFIG['TT_DATE'], 'stylesheet'=>CONFIG['DEFAULTTHEME'], 'language'=>CONFIG['DEFAULTLANG']
        ]);
        $invitees = "$id ".$current_user->invitees."";
        DB::run("UPDATE users SET invites = invites - 1, invitees=? WHERE id = ?", [$invitees,$current_user->id]);
        // Send Email
        $mess = strip_tags($_POST["mess"]);
        $names = CONFIG['SITENAME'];
        $links = URLROOT;
        $emailmain = CONFIG['SITEEMAIL'];
        
        $body = file_get_contents(VIEW_PATH . "/user/email/inviteuser.php");
        $body = str_replace("sitename%", $names, $body);
        $body = str_replace("%username%", $current_user->username, $body);
        $body = str_replace("%email%", $email, $body);
        $body = str_replace("%mess%", $mess, $body);
        $body = str_replace("%links%", $links, $body);
        $body = str_replace("%id%", $id, $body);
        $body = str_replace("%secret%", $secret, $body);

        $TTMail = new TTMail();
        $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
        showMessage(URLROOT, LANG["A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"] . " (" . htmlspecialchars($email) . "). " . LANG["ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"] . " <br/ >");
    }

    // Admin Fetch All Pending Users
    public static function InviteTree($user_id)
    {
        // Get Invitees Data
        $res = DB::newSelect('users', '*', ['status =','invited_by ='], ['confirmed',$user_id], 'ORDER BY username');
        $num = $res->rowCount();
        $invitees = DB::fetchColumn('users', 'count(id)', ['status =','invited_by ='], ['confirmed',$user_id], 'ORDER BY username');
        if ($invitees == 0) {
            showMessage("profile/$user_id", LANG["USER_NOT_INVITED"]);
        }
        
        $title = "Invite Tree for [<a href=" . URLROOT . "/profile/$user_id>" . $user_id . "</a>]";

        $data = [
            'res' => $res,
            'num' => $num,
            'title' => $title
        ];
        return $data;
    }

    // Invite Form Submit
    public static function submitInvite()
    {
        // Check User Input
        $url = Validate::Input('pending') == 0 ? '/admininvite' : '/admininvite/pending';
        $ids = Validate::multiIds('users');
        
        $res = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
        foreach ($res as $row) {
            # We remove the invitee from the inviter and give them back there invite.
            $invitees = str_replace("$row[id] ", "", $row["invitees"]);
            $views = $row["invites"] + 1;
            DB::update('users', ['invites','invitees'], ['id='], [$views,$invitees,$row['invited_by']]);
            UsersModel::deleteUser($row['id']);
        }

        return $url;
    }

    // Admin
    public static function pendingA()
    {
        // Pagination
        $count = DB::fetchColumn('users', 'COUNT(id)', ['status =', 'invited_by !='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite/pending?');
        $stmt = DB::run("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
        $array = ['invites'=>$stmt,'count'=>$count,'pager'=>$pager];
        
        return $array;
	}
	
    // Admin
    public static function inviteA()
    {
        // Pagination
        $count = DB::fetchColumn('users', 'COUNT(id)', ['status =', 'invited_by !='], ['confirmed',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite?');
        $users = DB::run("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u 
		LEFT JOIN users i ON u.invited_by = i.id 
		WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit")->fetchAll();
        $array = ['invites'=>$users,'count'=>$count,'pager'=>$pager];
        return $array;
    }

}