<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ForumsModel;

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
            ForumsModel::reorder($id, $move);
        }

        // Get Forum Cat Data
        $forumcat = ForumsModel::getCats();

        // Data For View
        $data = [
            'TITLE' => LANG["FORUMS"],
            'CATEGORY' => $forumcat
        ];

        // Load View
        $this->view('admin-forumcat', $data, 'admin');
    }

    // Add/Edit Cat
    public function submit()
    {
        ForumsModel::insertOrUpdate();
    }

    // Delete Forum Cat Submit
    public function delete($id, $action = 0)
    {
        ForumsModel::deleteCats($id, $action);
    }

}