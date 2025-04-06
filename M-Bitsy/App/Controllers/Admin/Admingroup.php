<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\GroupsLogic;

/*
* Group Controller
*/
class Admingroup extends Controller
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

    // Groups
    public function index()
    {
        if (!empty($_POST)) {
            // Add/Edit Groups
            GroupsLogic::insertOrUpdate();
        } else {
            // Get All Groups
            $groups = GroupsLogic::getGroups();
            
            $data = [
                'TITLE' => LANG["GROUPS"],
                'GROUPS' => $groups
            ];
        
            // Load View
            $this->template('admin-group', $data, 'admin');
        }
    }

    // Delete Groups
    public function delete($group_id)
    {
        GroupsLogic::delete($group_id);
    }

}