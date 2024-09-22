<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\PermissionsLogic;

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
            PermissionsLogic::insertOrUpdate();
        } else {
            // Get Permissions
            $permissions = PermissionsLogic::fetchAll('*', '', 'ORDER BY permission_id'); 
		
            // Data For View
            $data = [
                'TITLE' => LANG["GROUPS"],
                'PERMISSIONS' => $permissions
            ];
       
            // Load View
            $this->template('admin-permission', $data, 'admin');
        }
        
    }

    // Delete Permissions
    public function delete($id)
    {
        PermissionsLogic::delete($id);
    }

}