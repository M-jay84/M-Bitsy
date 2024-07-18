<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\BansModel;
use App\Libs\TTMail;
use App\Models\InvitesModel;
use App\Models\UsersModel;

/*
* Invite Controller
*/
class Invite 
{
    // Invite Default Page
    public function index()
    {
        $USER = new User(0, 2);
        // Check Invites On
        if (!CONFIG['INVITEONLY'] && !CONFIG['ENABLEINVITES']) {
            Redirect(Url(), Txt("INVITES_DISABLED_MSG"));
        }

        // Check User Has Invite
        if ($USER->has('invites') == 0) {
            Redirect(Url(), Txt("YOU_HAVE_NO_INVITES_MSG"));
        }

        // Check Max Users
        $users = UsersModel::fetchColumn('count(id)', ['enabled='], ['yes']);
        if ($users >= CONFIG['MAXUSERSINVITE']) {
            Redirect(Url(), "Sorry, The current user account limit (" . number_format(CONFIG['MAXUSERSINVITE']) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }

        // Init Data
        $data = [
            'V_title' => Txt('INVITES'),
            'USER' => $USER
        ];
        
        // Load View
        template('invite', $data, 'user');
    }

    // Invite Form Submit
    public function submit()
    {
        $USER = new User(0, 2);
        // Check User Input
        $email = Input('email', 'email');

        // Check Email Not Banned
        $message = BansModel::checkEmail($email);
        if ($message) {
            Redirect(Url(), $message);
        }

        // Set Invite
        $secret = mksecret();
        $username = "invite_" . mksecret(20);
        $id = UsersModel::insert([
            'username'=>$username, 'secret'=>$secret, 'email'=>$email, 'status'=>'pending',
            'invited_by'=>$USER->has('id'), 'added'=>get_date_time(), 'stylesheet'=>CONFIG['DEFAULTTHEME'], 'language'=>CONFIG['DEFAULTLANG']
        ]);
        $invitees = "$id ".$USER->has('invitees')."";
        InvitesModel::update($invitees, $USER->has('id'));
        
        // Send Email
        $mess = strip_tags($_POST["mess"]);
        $names = CONFIG['SITENAME'];
        $links = URLROOT;
        $emailmain = CONFIG['SITEEMAIL'];
        
        $body = file_get_contents(EMAILDIR . "/inviteuser.php");
        $body = str_replace("sitename%", $names, $body);
        $body = str_replace("%username%", $USER->has('username'), $body);
        $body = str_replace("%email%", $email, $body);
        $body = str_replace("%mess%", $mess, $body);
        $body = str_replace("%links%", $links, $body);
        $body = str_replace("%id%", $id, $body);
        $body = str_replace("%secret%", $secret, $body);

        $TTMail = new TTMail();
        $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
        Redirect(Url(), Txt("A_CONFIRMATION_EMAIL_HAS_BEEN_SENT") . " (" . htmlspecialchars($email) . "). " . Txt("ACCOUNT_CONFIRM_SENT_TO_ADDY_REST") . " <br/ >");
    }

    // User Invitee's Default Page todo invites
    public function invitetree($id)
    {
        $USER = new User(0, 2);
        // Check User
        if (!Id($id)) {
            $id = $USER->has('id');
        }

        // Get Invitees Data
        $res = UsersModel::fetchAll('*', ['status =','invited_by ='], ['confirmed',$id], 'ORDER BY username');
        $num = $res->rowCount();
        $invitees = UsersModel::fetchColumn('count(id)', ['status =','invited_by ='], ['confirmed',$id], 'ORDER BY username');
        if ($invitees == 0) {
            Redirect(Url("profile/$id"), Txt("USER_NOT_INVITED"));
        }

        // Set Title
        if ($id != $USER->has('id')) {
            $title = "Invite Tree for [<a href=" . URLROOT . "/profile/$id>" . $id . "</a>]";
        } else {
            $title = "You have $invitees invitees " . coloredname($USER->has('id')) . "";
        }

        // Init Data
        $data = [
            'V_title' => $title,
            'V_id' => $id,
            'V_res' => $res,
            'V_num' => $num,
            'USER' => $USER
        ];
        
        // Load View
        template('invite_tree', $data, 'user');
    }
    
}