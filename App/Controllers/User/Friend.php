<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\FriendsModel;

/*
* Friend Controller
*/
class Friend extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
    }

    // Friends Default Page
    public function index($userid, $type = 'friend')
    {
    
        // Get Friends Data
        $data['current_user'] = $this->current_user;
        $friend = (new FriendsModel)->join($userid, $data, $type);

        // Data For View
        $data = [
            'page_title' => "$type Lists For ".coloredname($userid)."",
            'V_userid' => $userid,
            'V_friends' => $friend,
            'V_type' => $type
        ];
        
        // Load View
        $this->view('friend', $data, 'user');
    }

    // Add Friend Submit
    public function action($type = 'friend', $targetid = 0, $del = 0)
    {
    
        $data['current_user'] = $this->current_user;
        if ($del) {
            (new FriendsModel)->delete($targetid, $data, $type);
        } else {
            (new FriendsModel)->add($targetid, $data, $type);
        }
    }

}