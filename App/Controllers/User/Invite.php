<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\BansModel;
use App\Libs\TTMail;
use App\Models\InvitesModel;
use App\Models\UsersModel;

/*
* Invite Controller
*/
class Invite extends Controller // todo
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        // Check Invites On
        if (!CONFIG['INVITEONLY'] && !CONFIG['ENABLEINVITES']) {
            $this->showMessage(URLROOT, LANG["INVITES_DISABLED_MSG"]);
        }
    }

    // Invite Default Page
    public function index()
    {
        // Check User Has Invite
        if ($this->current_user->invites == 0) {
            $this->showMessage(URLROOT, LANG["YOU_HAVE_NO_INVITES_MSG"]);
        }

        // Check Max Users
        $users = DB::fetchColumn('users', 'count(id)', ['enabled='], ['yes']);
        if ($users >= CONFIG['MAXUSERSINVITE']) {
            $this->showMessage(URLROOT, "Sorry, The current user account limit (" . number_format(CONFIG['MAXUSERSINVITE']) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
        }

        // Data For View
        $data = [
            'page_title' => LANG['INVITES']
        ];
        
        // Load View
        $this->view('user-invite', $data, 'user');
    }

    // Invite Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            (new InvitesModel())->sendInvite($this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
        $this->showMessage(URLROOT, LANG["SUCCESS"]);
    }

    // User Invitee's Default Page todo invites
    public function invitetree($id)
    {
        $invites = (new InvitesModel())->InviteTree($id);

        // Data For View
        $data = [
            'page_title' => $$invites['title'],
            'V_id' => $id,
            'V_res' => $$invites['res'],
            'V_num' => $$invites['num'],
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('user-invite_tree', $data, 'user');
    }
    
}