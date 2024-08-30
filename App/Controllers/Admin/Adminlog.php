<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\LogsModel;

/*
* Log Controller
*/
class Adminlog extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Log Default Page
    public function index()
    {
    
        $logs = (new LogsModel)->getAll();

        // Data For View
        $data = [
            'page_title' => LANG["SITE_LOG"],
            'V_pager' => $logs['pager'],
            'V_logs' => $logs['logs']
        ];

        // Load View
        $this->view('log', $data, 'admin');
    }

    // Log Form Submit
    public function delete()
    {
    
        (new LogsModel)->delete();
    }

}