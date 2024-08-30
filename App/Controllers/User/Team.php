<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TeamsModel;

/*
* Teams Controller
*/
class Team extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
    }

    // Teams Default Page
    public function index()
    {
    
        // Get Teams Data
        $teams = (new TeamsModel)->getTeams();

        // Data For View
        $data = [
            'page_title' => LANG["TEAM"],
            'V_teams' => $teams,
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('team', $data, 'user');
    }

    // Delete Team Form Submit
    public function delete($id)
    {

    
        (new TeamsModel)->delete($id, $this->current_user->name);
    }

    // Add/Edit Form Submit
    public function submit()
    {
    
        (new TeamsModel)->addOrUpdate($this->current_user->name);
    }

}