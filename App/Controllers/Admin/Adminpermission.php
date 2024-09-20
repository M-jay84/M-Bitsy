<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\PermissionsModel;

/*
* Permission Controller
*/
class Adminpermission extends Controller
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

    // Permissions
    public function index()
    {
        if (!empty($_POST)) {
            PermissionsModel::insertOrUpdate();
        } else {
            // Get Permissions
            $permissions = PermissionsModel::fetchAll('*', '', 'ORDER BY permission_id'); 
		
            // Data For View
            $data = [
                'TITLE' => LANG["GROUPS"],
                'PERMISSIONS' => $permissions
            ];
       
            // Load View
            $this->view('admin-permission', $data, 'admin');
        }
        
    }

    // Delete Permissions
    public function delete($id)
    {
        PermissionsModel::delete($id);
    }

}