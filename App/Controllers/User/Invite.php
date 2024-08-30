<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\BansModel;
use App\Libs\TTMail;
use App\Models\InvitesModel;
use App\Models\UsersModel;

/*
* Invite Controller
*/
class Invite extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
    }

    // Invite Default Page
    public function index()
    {
    
        // Check Invites On
        if (!CONFIG['INVITEONLY'] && !CONFIG['ENABLEINVITES']) {
            showMessage(Url(), LANG["INVITES_DISABLED_MSG"]);
        }

        // Check User Has Invite
        if ($this->current_user->invites == 0) {
            showMessage(Url(), LANG["YOU_HAVE_NO_INVITES_MSG"]);
        }

        // Check Max Users
        $users = UsersModel::fetchColumn('count(id)', ['enabled='], ['yes']);
        if ($users >= CONFIG['MAXUSERSINVITE']) {
            showMessage(Url(), "Sorry, The current user account limit (" . number_format(CONFIG['MAXUSERSINVITE']) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }

        // Data For View
        $data = [
            'page_title' => LANG['INVITES']
        ];
        
        // Load View
        $this->view('invite', $data, 'user');
    }

    // Invite Form Submit
    public function submit()
    {
    
        // Check User Input
        $email = Input('email', 'email');

        // Check Email Not Banned
        $message = (new BansModel)->checkEmail($email);
        if ($message) {
            showMessage(Url(), $message);
        }

        // Set Invite
        $secret = mksecret();
        $username = "invite_" . mksecret(20);
        $id = UsersModel::insert([
            'username'=>$username, 'secret'=>$secret, 'email'=>$email, 'status'=>'pending',
            'invited_by'=>$this->current_user->id, 'added'=>get_date_time(), 'stylesheet'=>CONFIG['DEFAULTTHEME'], 'language'=>CONFIG['DEFAULTLANG']
        ]);
        $invitees = "$id ".$this->current_user->invitees."";
        (new InvitesModel)->update($invitees, $this->current_user->id);
        
        // Send Email
        $mess = strip_tags($_POST["mess"]);
        $names = CONFIG['SITENAME'];
        $links = CONFIG['URLROOT'];
        $emailmain = CONFIG['SITEEMAIL'];
        
        $body = file_get_contents(CONFIG['APPROOT'] . "/Views/user/email/inviteuser.php");
        $body = str_replace("sitename%", $names, $body);
        $body = str_replace("%username%", $this->current_user->username, $body);
        $body = str_replace("%email%", $email, $body);
        $body = str_replace("%mess%", $mess, $body);
        $body = str_replace("%links%", $links, $body);
        $body = str_replace("%id%", $id, $body);
        $body = str_replace("%secret%", $secret, $body);

        $TTMail = new TTMail();
        $TTMail->Send($email, "$names user registration confirmation", $body, "", "-f$emailmain");
        showMessage(Url(), LANG["A_CONFIRMATION_EMAIL_HAS_BEEN_SENT"] . " (" . htmlspecialchars($email) . "). " . LANG["ACCOUNT_CONFIRM_SENT_TO_ADDY_REST"] . " <br/ >");
    }

    // User Invitee's Default Page todo invites
    public function invitetree($id)
    {
    
        // Check User
        if (!Id($id)) {
            $id = $this->current_user->id;
        }

        // Get Invitees Data
        $res = UsersModel::fetchAll('*', ['status =','invited_by ='], ['confirmed',$id], 'ORDER BY username');
        $num = $res->rowCount();
        $invitees = UsersModel::fetchColumn('count(id)', ['status =','invited_by ='], ['confirmed',$id], 'ORDER BY username');
        if ($invitees == 0) {
            showMessage(Url("profile/$id"), LANG["USER_NOT_INVITED"]);
        }

        // Set Title
        if ($id != $this->current_user->id) {
            $title = "Invite Tree for [<a href=" . CONFIG['URLROOT'] . "/profile/$id>" . $id . "</a>]";
        } else {
            $title = "You have $invitees invitees " . coloredname($this->current_user->id) . "";
        }

        // Data For View
        $data = [
            'page_title' => $title,
            'V_id' => $id,
            'V_res' => $res,
            'V_num' => $num,
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('invite_tree', $data, 'user');
    }
    
}