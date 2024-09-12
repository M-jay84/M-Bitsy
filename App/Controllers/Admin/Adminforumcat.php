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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Forum Cat Default Page
    public function index($id = 0, $move = false)
    {
        if ($move) {
            ForumsModel::reorder($id, $move);
        }

        // Get Forum Cat Data
        $forumcat = ForumsModel::getCats();

        // Data For View
        $data = [
            'page_title' => LANG["FORUMS"],
            'V_category' => $forumcat
        ];

        // Load View
        $this->view('admin-forumcat', $data, 'admin');
    }

    // Add Forum Cat Submit
    public function submit()
    {
        ForumsModel::insertOrUpdate();
    }

    // Delete Forum Cat Submit
    public function delete($id, $action = '')
    {
        ForumsModel::deleteCats($id, $action);
    }

}