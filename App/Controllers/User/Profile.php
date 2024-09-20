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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Profile
    public function index($id)
    {
        if ($this->current_user->view_users != "yes" && $this->current_user->id != $id) {
            showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }

        // Get User Profile
        $user = ProfileModel::getProfile($id, $this->current_user);

        if ($user['user']["privacy"] != "strong" || ($this->current_user->isStaff) || ($this->current_user->id == $id)) {

            // Data For View
            $data = [
                'TITLE' => LANG["USER_DETAILS_FOR"] . coloredname($id),
                'ID' => $id,
                'COUNT' => $user['count'],
                'DROPDOWN' => $user['dropdown'],
                'USER' => $user['user'],
                'HTML' => $user['html'],
                'current_user' => $this->current_user
            ];

            // Load View
            $this->view('user-profile', $data, 'user');
        } else {
            showMessage(URLROOT, LANG["USER_PRIVATE"]);
        }
    }

    // Edit Profile Form Submit
    public function submit($id)
    {
        // Check User
        if (!$this->current_user->isStaff or $this->current_user->id != $id) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($_POST)) {
            ProfileModel::submit($id, $this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Admin Edit Profile Form Submit
    public function submited($id)
    {
        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($_POST)) {
            ProfileModel::adminSubmited($id, $this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

    // Delete Profile Default
    public function delete($id)
    {
        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!empty($id)) {
            ProfileModel::deleteprofile($id, $this->current_user);
        } else {
            showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}