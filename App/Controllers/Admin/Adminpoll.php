<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\PollsModel;

/*
* Polls Controller
*/
class Adminpoll extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Polls Default Page
    public function index()
    {
    
        // Get Poll Data
        $polls = PollsModel::getAll();

        // Data For View
        $data = [
            'page_title' => LANG["POLLS"],
            'V_polls' => $polls
        ];

        // Load View
        $this->view('poll', $data, 'admin');
    }

    // Add/Edit Poll Form Submit
    public function submit()
    {
    
        // Add Insert / Edit Update
        PollsModel::insertOrUpdate();
    }

    // Delete Poll Form Submit
    public function delete($id)
    {
    
        PollsModel::delete($id);
    }

}