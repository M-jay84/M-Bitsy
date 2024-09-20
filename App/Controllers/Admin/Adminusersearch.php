<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersearchModel;

/*
* Search User Controller
*/
class Adminusersearch extends Controller
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

    // User Search
    public function index()
    {
        $search = $_POST['action'] ?? '0';
        if ($search == '1') {
            self::submit();
        }

        // Results From Post
        $results = UsersearchModel::searchUsers();

        // Data For View
        $data = [
            'TITLE' => LANG["USER_SEARCH"],
            'RESULTS' => $results['results'],
            'PAGER' => $results['pager'],
            'INPUTS' => $results['inputs'],
            'GROUPS' => $results['groups']
        ];

        // Load View
        $this->view('admin-user-search', $data, 'admin');
    }

    // Submit Warn/Disable Form
    private function submit() // todo tidy up
    {
        if (!empty($_POST["ids"])) {
            UsersearchModel::submitUser($this->current_user);
        } else {
            showMessage('adminusersearch', LANG["MISSING_ID"]);
        }
    }
    
}