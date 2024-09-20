<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Cleanup;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\ShoutboxModel;

/*
* Shoutbox Controller
*/
class Shoutbox extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    public function index(){}

    // Ajax Shoutbox Load
    public function chat()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['SHOUTBOX'],
            'SHOUTS' => ShoutboxModel::shouts(20),
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('user-shoutboxload', $data);
    }

    // Add Shout Submit
    public function add()
    {
        ShoutboxModel::add($this->current_user);
    }

    // Delete Shout Submit
    public function delete($id, $sure = 0)
    {
        ShoutboxModel::delete($id, $this->current_user, $sure);
    }

    // Edit Shout Default Page
    public function edit()
    {
        ShoutboxModel::edit($this->current_user);
    }

    // Shoutbox History Default Page
    public function history()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['HISTORY'],
            'SHOUTS' => ShoutboxModel::shouts(80)
        ];

        // Load View
        $this->view('user-shoutbox', $data, 'user');
    }

    // Run Cleanup
    public function cleanup()
    {
        DB::update('tasks', ['last_time'], ['task='], [CONFIG['TT_TIME'],'cleanup']);
        Cleanup::run();
        showMessage('admincp', LANG["FORCE_CLEAN_COMPLETED"]);
    }

}