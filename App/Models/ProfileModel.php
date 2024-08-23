<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\FriendsModel;
use App\Models\CountriesModel;
use App\Models\SnatchedModel;
use App\Models\StylesheetsModel;
use App\Models\PostsModel;
use App\Models\TorrentsModel;
use App\Models\CommentsModel;
use App\Models\GroupsModel;
use App\Models\MessagesModel;
use App\Models\LogsModel;

/*
* Users Table Data
*/
class ProfileModel
{
    // Get User Profile
    public function getProfile($id, $current_user)
    {
        $user = DB::fetchRow('users', '*', ['id='], [$id]);

        if (!$user) {
            showMessage(Url(), LANG["NO_USER"] . " $id.");
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && !$current_user->edit_users == 'yes') {
            showMessage(Url(), LANG["NO_ACCESS_ACCOUNT_DISABLED"]);
        }

        // Start Blocked Users
        $show = (new FriendsModel)->fetchRow('id', ['userid=','friend=','friendid='], [$user['id'],'enemy',$current_user->id]);

        if ($show && $current_user->control_panel != "yes") {
            showMessage(Url(), LANG["USER_PRIVATE"]);
        }

        $dropdown['stylesheets'] = (new StylesheetsModel)->getStyleDropDown($user['stylesheet']);
        $dropdown['countries'] = (new CountriesModel)->dropdown($user['country']);
        $dropdown['tz'] = timeZoneDropDown($user['tzoffset']);
        $dropdown['teams'] = (new TeamsModel)->dropDownTeams($user['team']);
        $dropdown['gender'] = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . LANG["MALE"] . "</option>\n"
            . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . LANG["FEMALE"] . "</option>\n";

        $count['numtorrents'] = (new TorrentsModel)->fetchColumn('count(id)', ['owner='], [$id]);
        $count['numcomments'] = (new CommentsModel)->fetchColumn('count(id)', ['user='], [$id]);
        $count['numforumposts'] = (new PostsModel)->fetchColumn('count(id)', ['userid='], [$id]);
        $count['numhnr'] = (new SnatchedModel)->fetchColumn('COUNT(`hnr`)', ['uid='], [$id]);

        $html['userratio'] = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---";
        $html['avatar'] = htmlspecialchars($user["avatar"]) ? htmlspecialchars($user["avatar"]) : CONFIG['URLROOT'] . "/assets/images/misc/default_avatar.webp";
        $html['usersignature'] = stripslashes($user["signature"]);
        $html['team'] = (new TeamsModel)->getTeam($user['team']);
        $html['country'] = CountriesModel::fetchRow($user['country']);
        $friend = (new FriendsModel)->countFriendAndEnemy($current_user->id, $id);

        // Init Data
        $data = [
            'dropdown' => $dropdown,
            'count' => $count,
            'user' => $user,
            'html' => $html,
            'friend' => $friend
        ];
        return $data;
    }

    // Edit Profile Form Submit
    public function submit($id, $data)
    {

        if (!empty($_POST)) {
            // Init Data
            $data = [
                'privacy' => Input('privacy'),
                'notifs' => Input('pmnotif') == 'yes' ? "[pm]" : "",
                'stylesheet' => Input('stylesheet'),
                'client' => Input('client'),
                'age' => Input('age', 'int'),
                'gender' => Input('gender'),
                'country' => Input('country'),
                'team' => Input('teams'),
                'avatar' => Input('avatar'),
                'title' => Input('title'),
                'hideshoutbox' => Input('hideshoutbox') == 'yes' ? 'yes' : 'no',
                'tzoffset' => Input('tzoffset', 'int'),
                'signature' => Input('signature')
            ];

            if ($_POST['resetpasskey']) {
                $rand = array_sum(explode(" ", microtime()));
                $passkey = md5($data['current_user']->username . $rand . $data['current_user']->secret . ($rand * mt_rand()));
                $data['passkey'] = $passkey;
            }

            // Update
            UsersModel::updateset($data, $id);
            showMessage(Url("profile/$id"), LANG["UPDATE_COMPLETE"]);
        }
    }

    // Admin Edit Profile Form Submit
    public function adminSubmited($id, $data)
    {
        $email = Input('email', 'email');

        // Check User
        if ($data['current_user']->class < 5 && $id != $data['current_user']->id) {
            showMessage(Url("profile/$id"), LANG["NO_PERMISSION"]);
        }

        // Init Data
        $data = [
            'downloaded' => strtobytes(Input('downloaded')),
            'uploaded' => strtobytes(Input('uploaded')),
            'ip' => Input('ip'),
            'class' => Input('class'),
            'donated' => Input('donated'),
            'password' => Input('password'),
            'warned' => Input('warned'),
            'downloadbanned' => Input('downloadbanned'),
            'shoutboxpos' => Input('shoutboxpos'),
            'modcomment' => Input('modcomment'),
            'enabled' => Input('enabled'),
            'invites' => Input('invites'),
            'email' => $email,
            'seedbonus' => Input('bonus'),
        ];

        // Change User Class
        if ($data['class'] != 0 && $data['class'] != $data['current_user']->class) {
            // change user class
            $arr = UsersModel::fetchRow('class', ['id ='], [$id]);
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $data['class'] && $uc > $data['current_user']->class) {
                showMessage(Url(), LANG["YOU_CANT_DEMOTE_YOURSELF"]);
            } elseif ($uc == $data['current_user']->class) {
                showMessage(Url(), LANG["YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"]);
            } else {
                UsersModel::update(['class'], ['id='], [$data['class'], $id]);
                // Notify user
                $prodemoted = ($data['class'] > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . (new GroupsModel)->GetUserClassName($data['class'], $data['current_user']) . " by " . $data['current_user']->username . "";
                MessagesModel::insert(['sender' => 0, 'receiver' => $id, 'added' => get_date_time(), 'subject' => 'Group Change', 'msg' => $msg]);
            }
        }

        // Reset Passkey Check
        if ($_POST['resetpasskey'] == 'yes') {
            $rand = array_sum(explode(" ", microtime()));
            $passkey = md5($data['current_user']->username . $rand . $data['current_user']->secret . ($rand * mt_rand()));
            $data = ['passkey' => $passkey];
        }

        // Change Password
        $chgpasswd = Input('chgpasswd') == 'yes' ? true : false;
        if ($chgpasswd) {
            $passres = UsersModel::fetchRow('password', ['id ='], [$id]);
            if ($data['password'] != $passres['password']) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $data = ['password' => $password];
                (new LogsModel)->write($data['current_user']->username . " has changed password for user: $id");
            }
        }

        // Update
        UsersModel::updateset($data, $id);
        (new LogsModel)->write($data['current_user']->username . " has edited user: $id details");
        showMessage(Url("profile/$id"), LANG["UPDATE_COMPLETE"]);
    }

    // Delete Profile Default Page otdo (tidy up)
    public function deleteprofile($data)
    {
        // Check User Input
        $userid = Input('userid', 'int');
        $delreason = Input('delreason', 'int');
        $sure = Input('sure', 'int');

        // Check User
        if (!$data['current_user']->delete_users) {
            showMessage(Url("profile/$userid"), LANG["NO_PERMISSION"]);
        }

        if (!Id($userid)) {
            showMessage(Url("profile/$userid"), LANG["MISSING_ID"]);
        }

        if ($data['current_user']->id == $userid) {
            showMessage(Url("profile/$userid"), LANG["YOU_CANT_DELETE_ADMIN"]);
        }

        if (!$delreason) {
            showMessage(Url("profile/$userid"), LANG["MISSING_FORM_DATA"]);
        }

        if ($sure == "0") {
            showMessage(Url('profile/delete'), "Sanity check: You are about to delete user. Click <a href='" . CONFIG['URLROOT'] . "/profile/delete?sure=1'>here</a> if you are sure.");
        }

        // Delete Profile
        UsersModel::deleteUser($userid);
        (new LogsModel)->write($data['current_user']->username . " has deleted account: " . coloredname($userid) . "");
        showMessage(Url("profile/$userid"), LANG["DEL_COMPLETE"]);
    }

}