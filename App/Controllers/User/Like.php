<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\LikesModel;

/*
* Like Controller
*/
class Like 
{
    // Like Submit
    public function index($id, $type)
    {
        $USER = new User(0, 2);
        LikesModel::switch($id, $type);
    }

}