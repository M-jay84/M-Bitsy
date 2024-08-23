<?php

namespace App\Models;

use App\Libs\DB;

/*
* Invites Table Data
*/
class InvitesModel
{
    // Update Invites
    public function update($invitees, $id)
    {
        $sql = DB::run("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = ".$id."");

        return $sql;
    }
	
    // Admin Fetch All Invited Users
    public function invitedUser($limit)
    {
        $stmt = DB::run("SELECT u.id, u.username, u.email, u.added, u.last_access, u.class, u.invited_by, i.username as inviter FROM users u 
		LEFT JOIN users i ON u.invited_by = i.id 
		WHERE u.status = 'confirmed' AND u.invited_by != '0' ORDER BY u.added DESC $limit")->fetchAll();
		return $stmt;
	}

    // Admin Check Invite
    public function checkInvite($ids)
    {
        $stmt = DB::run("SELECT u.id, u.invited_by, i.invitees FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' AND u.id IN ($ids)");
		return $stmt;
	}
	
    // Admin Fetch All Pending Users
    public function pendingUser($limit)
    {
        $stmt = DB::run("SELECT u.id, u.username, u.email, u.added, u.invited_by, i.username as inviter FROM users u LEFT JOIN users i ON u.invited_by = i.id WHERE u.status = 'pending' AND u.invited_by != '0' ORDER BY u.added DESC $limit");
		return $stmt;
    }

}