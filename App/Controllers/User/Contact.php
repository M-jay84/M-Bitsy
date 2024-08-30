<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\StaffMessagesModel;

/*
* Confirm Controller
*/
class Contact extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(1);
    }

    // Contact Us Page
    public function index()
    {
        // Data For View
        $data = [
            'page_title' => 'Contact Staff'
        ];

        // Load View
        $this->view('contact', $data, 'user');
    }

    // Contact Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            (new StaffMessagesModel)->insert();
        } else {
            showMessage(Url('contact'), LANG["NOTHING_FOUND"]);
        }
    }
}