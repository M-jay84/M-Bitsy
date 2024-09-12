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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Comments Default Page
    public function index()
    {
        // Get Data
        $comments = CommentsModel::grabAll();
        
        // Data For View
        $data = [
            'page_title' => LANG["COMMENT"],
            'V_comments' => $comments['stmt'],
            'V_pager' => $comments['pager']
        ];

        // Load View
        $this->view('admin-comment', $data, 'admin');
    }

}