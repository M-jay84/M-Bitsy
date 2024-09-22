<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsLogic;

/*
* forum Cats Controller
*/
class AdminforumCat extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Forum Cats
    public function index($id = 0, $move = false)
    {
        if ($move) {
            // Sort/mMove Order
            ForumsLogic::reorder($id, $move);
        }

        // Get Forum Cat Data
        $forumcat = ForumsLogic::getCats();

        // Data For View
        $data = [
            'TITLE' => LANG["FORUMS"],
            'CATEGORY' => $forumcat
        ];

        // Load View
        $this->template('admin-forumcat', $data, 'admin');
    }

    // Add/Edit Cat
    public function submit()
    {
        ForumsLogic::insertOrUpdate();
    }

    // Delete Forum Cat Submit
    public function delete($id, $action = 0)
    {
        ForumsLogic::deleteCats($id, $action);
    }

}