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
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Permissions Default Page
    public function index()
    {
        if (!empty($_POST)) {
            (new PermissionsModel)->insertOrUpdate();
        } else {
            // Get Permissions Data
            $permissions = (new PermissionsModel)->fetchAll('*', '', 'ORDER BY permission_id'); 
		
            // Data For View
            $data = [
                'page_title' => LANG["GROUP"],
                'V_permissions' => $permissions
            ];
       
            // Load View
            $this->view('permission', $data, 'admin');
        }
        
    }

    // Delete Permissions Submit
    public function delete($id)
    {
        (new PermissionsModel)->delete($id);
    }

}