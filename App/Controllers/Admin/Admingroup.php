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
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Groups Default Page
    public function index()
    {
        if (!empty($_POST)) {
            (new GroupsModel)->insertOrUpdate();
        } else {
            // Get Groups Data
            $groups = (new GroupsModel)->getGroups(); 
        
            // Data For View
            $data = [
                'page_title' => LANG["GROUP"],
                'V_groups' => $groups
            ];
        
            // Load View
            $this->view('group', $data, 'admin');
        }
    }

    // Delete Groups Submit
    public function delete($group_id)
    {
        (new GroupsModel)->delete($group_id);
    }

}