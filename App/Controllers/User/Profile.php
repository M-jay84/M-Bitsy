<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\ProfileModel;

/*
* Profile Controller
*/
class Profile
{
    // Profile Default Page
    public function index($id)
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_users', $id)) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }

        $user = ProfileModel::getProfile($USER, $id);

        if ($user['user']["privacy"] != "strong" || ($USER->has('control_panel') == "yes") || ($USER->has('id') == $id)) {

            // Init Data
            $data = [
                'V_title' => Txt("USER_DETAILS_FOR") . coloredname($id),
                'V_id' => $id,
                'V_count' => $user['count'],
                'V_dropdown' => $user['dropdown'],
                'V_user' => $user['user'],
                'V_friend' => $user['friend'],
                'V_html' => $user['html'],
                'USER' => $USER
            ];

            // Load View
            template('profile', $data, 'user');
        } else {
            Redirect(Url(), Txt("USER_PRIVATE"));
        }
    }

    // Edit Profile Form Submit
    public function submit($id)
    {
        $USER = new User(0, 2);
        // Check User
        if ($USER->isStaff($id)) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }
        ProfileModel::submit($USER, $id);
    }

    // Admin Edit Profile Form Submit
    public function submited($id)
    {
        $USER = new User(0, 2);
        ProfileModel::adminSubmited($USER, $id);
    }

    // Delete Profile Default Page otdo (tidy up)
    public function delete()
    {
        $USER = new User(0, 2);
        ProfileModel::deleteprofile($USER);
    }
}