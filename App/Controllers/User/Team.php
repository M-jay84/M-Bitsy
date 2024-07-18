<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TeamsModel;

/*
* Teams Controller
*/
class Team 
{
    // Teams Default Page
    public function index()
    {
        $USER = new User(0, 2);
        // Get Teams Data
        $teams = TeamsModel::getTeams();

        // Init Data
        $data = [
            'V_title' => Txt("TEAM"),
            'V_teams' => $teams,
            'USER' => $USER
        ];
        
        // Load View
        template('team', $data, 'user');
    }

    // Delete Team Form Submit
    public function delete($id)
    {
        $USER = new User(0, 2);
        TeamsModel::delete($id);
    }

    // Add/Edit Form Submit
    public function submit()
    {
        $USER = new User(0, 2);
        TeamsModel::addOrUpdate();
    }

}