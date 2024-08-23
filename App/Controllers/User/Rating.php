<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\RatingsModel;

/*
* Rating Controller
*/
class Rating extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Torrent Rating Submit
    public function index($id)
    {
        (new RatingsModel)->addRating($id, $this->current_user->id);
    }

}