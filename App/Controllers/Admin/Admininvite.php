<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\InvitesModel;

/*
* Invited Users Controller
*/
class Admininvite extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Invited Default Page
    public function index()
    {
        // Show Invites
        $invite = InvitesModel::inviteA();

        // Data For View
        $data = [
            'TITLE' => LANG["INVITED_USER"],
            'COUNT' => $invite['count'],
            'PAGER' => $invite['pager'],
            'INVITES' => $invite['invites']
        ];
 
        // Load View
        $this->view('admin-invited-users', $data, 'admin');
    }

    // Invite Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            InvitesModel::submitInvite();
        } else {
            showMessage(URLROOT, LANG['NOTHING_SELECTED']);
        }
    }

    // Pendings Invites Default Page
    public function pending()
    {
        // Show Pending Invites
        $invite = InvitesModel::pendingA();

        // Data For View
        $data = [
            'TITLE' => LANG["PENDING_INVITE"],
            'COUNT' => $invite['count'],
            'PAGER' => $invite['pager'],
            'INVITES' => $invite['invites']
        ];

        // Load View
        $this->view('admin-invites-pending', $data, 'admin');
    }

}