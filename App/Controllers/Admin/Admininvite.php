<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Libs\Validate;
use App\Models\InvitesModel;
use App\Models\UsersModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Invited Default Page
    public function index()
    {
        // Pagination
        $count = DB::fetchColumn('users', 'COUNT(id)', ['status =', 'invited_by !='], ['confirmed',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite?');
        $users = InvitesModel::invitedUser($limit);

        // Data For View
        $data = [
            'page_title' => LANG["INVITED_USER"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users
        ];
 
        // Load View
        $this->view('admin-invited-users', $data, 'admin');
    }

    // Invite Form Submit
    public function submit()
    {
        // Check User Input
        if (!empty($_POST)) {
            $url = InvitesModel::submitInvite();
            $this->showMessage(URLROOT . $url, LANG["DEL_COMPLETE"]);
        } else {
            $this->showMessage(URLROOT, LANG['NOTHING_FOUND']);
        }
    }

    // Pendings Invites Default Page
    public function pending()
    {
        // Pagination
        $count = DB::fetchColumn('users', 'COUNT(id)', ['status =', 'invited_by !='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite/pending?');
        $stmt = InvitesModel::pendingUser($limit);

        // Data For View
        $data = [
            'page_title' => LANG["PENDING_INVITE"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $stmt
        ];

        // Load View
        $this->view('admin-invites-pending', $data, 'admin');
    }

}