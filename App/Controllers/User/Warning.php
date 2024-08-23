<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\WarningsModel;

/*
* Warning Controller
*/
class Warning extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // User Warnings Page
    public function index($user_id)
    {
        // Check Current User
        if (!$this->current_user->staff && $this->current_user->id != $user_id) {
            showMessage(CONFIG['APPROOT'], LANG['NO_PERMISSION']);
        }

        // Get Warnings Data
        $warnings = (new WarningsModel)->getByUserId($user_id);

        // Data For View
        $data = array(
            'page_title' => LANG["USER_DETAILS_FOR"] . coloredname($user_id),
            'user_id' => $user_id,
            'warnings' => $warnings,
            'current_user' => $this->current_user
        );

        // Load View
        $this->view('warning', $data, 'user');
    }

    // Warnings Form Submit
    public function submit($userid)
    {
        // Check User Input
        $data = [
            'reason' => Input('reason'),
            'expiry' => Input('expiry', 'int'),
            'type' => Input('type'),
            'current_user' => $this->current_user,
            'userid' => $userid
        ];

        // Check Input
        if (!$data['reason'] || !$data['expiry'] || !$data['type']) {
            showMessage(Url("profile/$data[userid]"), LANG["MISSING_FORM_DATA"]);
        }

        // Check User
        if (!$this->current_user->staff_page == 'yes') {
            showMessage(Url("profile/$data[userid]"), LANG["NO_PERMISSION"]);
        }

        // Add New Warning
        (new WarningsModel)->newwarn($data);
    }

    // Delete User Warning
    public function delete($userid)
    {
        // Check User
        if (!$this->current_user->staff_page == 'yes') {
            showMessage(Url("profile/$userid"), LANG["NO_PERMISSION"]);
        }

        // Set Data
        $data = [
            'current_user' => $this->current_user,
            'userid' => $userid,
            'post_id' => multiIds("ids")
        ];

        // Delete Warning/s
        (new WarningsModel)->deleteAll($data);
    }
}