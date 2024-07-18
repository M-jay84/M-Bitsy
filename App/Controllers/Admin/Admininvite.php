<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\InvitesModel;
use App\Models\UsersModel;

/*
* Invited Users Controller
*/
class Admininvite 
{
    // Invited Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        // Pagination
        $count = UsersModel::fetchColumn('COUNT(id)', ['status =', 'invited_by !='], ['confirmed',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite?');
        $users = InvitesModel::invitedUser($limit);

        // Init Data
        $data = [
            'V_title' => Txt("INVITED_USER"),
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $users,
            'USER' => $USER
        ];
 
        // Load View
        template('invited_users', $data, 'admin');
    }

    // Invite Form Submit
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        // Check User Input
        $url = Input('pending') == 0 ? '/admininvite' : '/admininvite/pending';
        $ids = multiIds('users');
        
        $res = InvitesModel::checkInvite($ids);
        foreach ($res as $row) {
            # We remove the invitee from the inviter and give them back there invite.
            $invitees = str_replace("$row[id] ", "", $row["invitees"]);
            $views = $row["invites"] + 1;
            UsersModel::update(['invites','invitees'], ['id='], [$views,$invitees,$row['invited_by']]);
            UsersModel::deleteUser($row['id']);
        }
        Redirect(URLROOT . $url, Txt("DEL_COMPLETE"));
    }

    // Pendings Invites Default Page
    public function pending()
    {
        $USER = new User(_MODERATOR, 2);
        // Pagination
        $count = UsersModel::fetchColumn('COUNT(id)', ['status =', 'invited_by !='], ['pending',0]);
        list($pager, $limit) = pagination(25, $count, URLROOT.'/admininvite/pending?');
        $stmt = InvitesModel::pendingUser($limit);

        // Init Data
        $data = [
            'V_title' => Txt("PENDING_INVITE"),
            'V_count' => $count,
            'V_pager' => $pager,
            'V_users' => $stmt,
            'USER' => $USER
        ];

        // Load View
        template('invites_pending', $data, 'admin');
    }

}