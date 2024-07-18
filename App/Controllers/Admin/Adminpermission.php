<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\PermissionsModel;

/*
* Permission Controller
*/
class Adminpermission 
{
    // Permissions Default Page
    public function index()
    {
        $USER = new User(_SUPERMODERATOR, 2);
        if (!empty($_POST)) {
            PermissionsModel::insertOrUpdate();
        } else {
            // Get Permissions Data
            $permissions = PermissionsModel::fetchAll('*', '', 'ORDER BY permission_id'); 
		
            // Init Data
            $data = [
                'V_title' => Txt("GROUP"),
                'V_permissions' => $permissions,
                'USER' => $USER
            ];
       
            // Load View
            template('permission', $data, 'admin');
        }
        
    }

    // Delete Permissions Submit
    public function delete($id)
    {
        $USER = new User(_SUPERMODERATOR, 2);
        PermissionsModel::delete($id);
    }

}