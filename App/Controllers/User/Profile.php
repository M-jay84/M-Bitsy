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
        $this->current_user = (new Auth)->user(2);
    }

    // Profile Default Page
    public function index($id)
    {
    
        if ($this->current_user->view_users != "yes" && $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_USER_VIEW"]);
        }

        $user = (new ProfileModel)->getProfile($id, $this->current_user);

        if ($user['user']["privacy"] != "strong" || ($this->current_user->control_panel == "yes") || ($this->current_user->id == $id)) {

            // Data For View
            $data = [
                'page_title' => LANG["USER_DETAILS_FOR"] . coloredname($id),
                'V_id' => $id,
                'V_count' => $user['count'],
                'V_dropdown' => $user['dropdown'],
                'V_user' => $user['user'],
                'V_friend' => $user['friend'],
                'V_html' => $user['html'],
            'current_user' => $this->current_user
            ];

            // Load View
            $this->view('profile', $data, 'user');
        } else {
            showMessage(Url(), LANG["USER_PRIVATE"]);
        }
    }

    // Edit Profile Form Submit
    public function submit($id)
    {
    
        // Check User
        if ($this->current_user->class < CONFIG['_MODERATOR'] or $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        }
        (new ProfileModel)->submit($id, $this->current_user);
    }

    // Admin Edit Profile Form Submit
    public function submited($id)
    {
    
        (new ProfileModel)->adminSubmited($id, $this->current_user);
    }

    // Delete Profile Default Page otdo (tidy up)
    public function delete()
    {
        (new ProfileModel)->deleteprofile($this->current_user);
    }
}