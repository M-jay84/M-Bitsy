<?php

namespace App\Controllers\User;

use App\Libs\Auth;
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
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Torrent Rating Submit
    public function index($id)
    {
        if (!empty($_POST)) {
            RatingsModel::addRating($id, $this->current_user->id);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}