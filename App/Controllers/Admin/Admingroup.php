<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\GroupsModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Groups Default Page
    public function index()
    {
        if (!empty($_POST)) {
            GroupsModel::insertOrUpdate();
        } else {
            
            $data = [
                'page_title' => LANG["GROUP"],
                'V_groups' => GroupsModel::getGroups()
            ];
        
            // Load View
            $this->view('admin-group', $data, 'admin');
        }
    }

    // Delete Groups Submit
    public function delete($group_id)
    {
        GroupsModel::delete($group_id);
    }

}