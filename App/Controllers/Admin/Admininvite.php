<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
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
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Invited Default Page
    public function index()
    {
    
        // Pagination
        $count = UsersModel::fetchColumn('COUNT(id)', ['status =', 'invited_by !='], ['confirmed',0]);
        list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'].'/admininvite?');
        $users = (new InvitesModel)->invitedUser($limit);

        // Data For View
        $data = [
            'page_title' => LANG["INVITED_USER"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users
        ];
 
        // Load View
        $this->view('invited_users', $data, 'admin');
    }

    // Invite Form Submit
    public function submit()
    {
    
        // Check User Input
        $url = Input('pending') == 0 ? '/admininvite' : '/admininvite/pending';
        $ids = multiIds('users');
        
        $res = (new InvitesModel)->checkInvite($ids);
        foreach ($res as $row) {
            # We remove the invitee from the inviter and give them back there invite.
            $invitees = str_replace("$row[id] ", "", $row["invitees"]);
            $views = $row["invites"] + 1;
            UsersModel::update(['invites','invitees'], ['id='], [$views,$invitees,$row['invited_by']]);
            UsersModel::deleteUser($row['id']);
        }
        showMessage(Url(CONFIG['URLROOT'] . $url), LANG["DEL_COMPLETE"]);
    }

    // Pendings Invites Default Page
    public function pending()
    {
    
        // Pagination
        $count = UsersModel::fetchColumn('COUNT(id)', ['status =', 'invited_by !='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, CONFIG['URLROOT'].'/admininvite/pending?');
        $stmt = (new InvitesModel)->pendingUser($limit);

        // Data For View
        $data = [
            'page_title' => LANG["PENDING_INVITE"],
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $stmt
        ];

        // Load View
        $this->view('invites_pending', $data, 'admin');
    }

}