<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\GroupsModel;

/*
* Group Controller
*/
class Admingroup 
{
    // Groups Default Page
    public function index()
    {
        $USER = new User(_SUPERMODERATOR, 2);
        if (!empty($_POST)) {
            GroupsModel::insertOrUpdate();
        } else {
            // Get Groups Data
            $groups = GroupsModel::getGroups(); 
        
            // Init Data
            $data = [
                'V_title' => Txt("GROUP"),
                'V_groups' => $groups,
                'USER' => $USER
            ];
        
            // Load View
            template('group', $data, 'admin');
        }
    }

    // Delete Groups Submit
    public function delete($group_id)
    {
        $USER = new User(_SUPERMODERATOR, 2);
        GroupsModel::delete($group_id);
    }

}