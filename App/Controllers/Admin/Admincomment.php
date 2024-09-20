<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CommentsModel;

/*
* Comment Controller
*/
class Admincomment extends Controller
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

    // Comments Page
    public function index()
    {
        // Get All Comments
        $comments = CommentsModel::grabAll();
        
        // Data For View
        $data = [
            'TITLE' => LANG["COMMENT"],
            'COMMENTS' => $comments['stmt'],
            'PAGER' => $comments['pager']
        ];

        // Load View
        $this->view('admin-comment', $data, 'admin');
    }

}