<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\LikesModel;

/*
* Like Controller
*/
class Like extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
    }

    // Like Submit
    public function index($id, $type)
    {
        $data['current_user'] = $this->current_user;
    
        (new LikesModel)->switch($id, $type, $data);
    }

}