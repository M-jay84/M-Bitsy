<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\RatingsModel;

/*
* Rating Controller
*/
class Rating 
{
    // Torrent Rating Submit
    public function index($id)
    {
        $USER = new User(0, 2);
        RatingsModel::addRating($id);
    }

}