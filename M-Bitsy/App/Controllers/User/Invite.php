<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\InvitesLogic;
use App\Models\UsersLogic;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        // Check Invites On
        if (!CONFIG['INVITEONLY'] && !CONFIG['ENABLEINVITES']) {
            showMessage(URLROOT, LANG["INVITES_DISABLED_MSG"]);
        }
    }

    // Invites
    public function index()
    {
        // Check User Has Invite
        if ($this->current_user->invites == 0) {
            showMessage(URLROOT, LANG["YOU_HAVE_NO_INVITES_MSG"]);
        }

        // Check Max Users
        UsersLogic::gmaxUser();

        // Data For View
        $data = [
            'TITLE' => LANG['INVITES']
        ];
        
        // Load View
        $this->template('user-invite', $data, 'user');
    }

    // Invite Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            InvitesLogic::sendInvite($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_FOUND"]);
        }
    }

    // User Invitee's
    public function invitetree($id)
    {
        // Get Users Invitees
        $invites = InvitesLogic::InviteTree($id);

        // Data For View
        $data = [
            'TITLE' => $$invites['title'],
            'INVITES' => $$invites['res'],
            'COUNT' => $$invites['num']
        ];
        
        // Load View
        $this->template('user-invite_tree', $data, 'user');
    }
    
}