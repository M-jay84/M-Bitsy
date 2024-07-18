<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\FriendsModel;

/*
* Friend Controller
*/
class Friend 
{
    // Friends Default Page
    public function index($userid, $type = 'friend')
    {
        $USER = new User(0, 2);
        // Get Friends Data
        $friend = FriendsModel::join($USER, $userid, $type);

        // Init Data
        $data = [
            'V_title' => "$type Lists For ".coloredname($userid)."",
            'V_userid' => $userid,
            'V_friends' => $friend,
            'V_type' => $type,
            'USER' => $USER
        ];
        
        // Load View
        template('friend', $data, 'user');
    }

    // Add Friend Submit
    public function action($type = 'friend', $targetid = 0, $del = 0)
    {
        $USER = new User(0, 2);
        if ($del) {
            FriendsModel::delete($targetid, $type);
        } else {
            FriendsModel::add($targetid, $type);
        }
    }

}