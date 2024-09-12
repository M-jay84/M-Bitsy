<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ProfileModel;

/*
* Profile Controller
*/
class Profile extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Profile Default Page
    public function index($id)
    {
        if ($this->current_user->view_users != "yes" && $this->current_user->id != $id) {
            $this->showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }

        $user = ProfileModel::getProfile($id, $this->current_user);

        if ($user['user']["privacy"] != "strong" || ($this->current_user->isStaff) || ($this->current_user->id == $id)) {

            // Data For View
            $data = [
                'page_title' => LANG["USER_DETAILS_FOR"] . coloredname($id),
                'V_id' => $id,
                'V_count' => $user['count'],
                'V_dropdown' => $user['dropdown'],
                'V_user' => $user['user'],
                'V_html' => $user['html'],
                'current_user' => $this->current_user
            ];

            // Load View
            $this->view('user-profile', $data, 'user');
        } else {
            $this->showMessage(URLROOT, LANG["USER_PRIVATE"]);
        }
    }

    // Edit Profile Form Submit
    public function submit($id)
    {
        // Check User
        if (!$this->current_user->isStaff or $this->current_user->id != $id) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($_POST)) {
            ProfileModel::submit($id, $this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Admin Edit Profile Form Submit
    public function submited($id)
    {
        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($_POST)) {
            ProfileModel::adminSubmited($id, $this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Delete Profile Default Page otdo (tidy up)
    public function delete()
    {
        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($_POST)) {
            ProfileModel::deleteprofile($this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}