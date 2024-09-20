<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\CommentsModel;
use App\Models\CountriesModel;
use App\Models\GroupsModel;
use App\Models\LogsModel;
use App\Models\MessagesModel;
use App\Models\PostsModel;
use App\Models\SnatchedModel;
use App\Models\TorrentsModel;

/*
* Users Table Data
*/
class ProfileModel
{
    // Get User Profile
    public static function getProfile($id, $current_user)
    {
        $user = DB::fetchRow('users', '*', ['id='], [$id]);

        if (!$user) {
            showMessage(URLROOT, LANG["NO_USER"] . " $id.");
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && !$current_user->edit_users == 'yes') {
            showMessage(URLROOT, LANG["NO_ACCESS_ACCOUNT_DISABLED"]);
        }

        $dropdown['countries'] = CountriesModel::dropdown($user['country']);
        $dropdown['tz'] = timeZoneDropDown($user['tzoffset']);
        $dropdown['gender'] = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . LANG["MALE"] . "</option>\n"
            . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . LANG["FEMALE"] . "</option>\n";

        $count['numtorrents'] = TorrentsModel::fetchColumn('count(id)', ['owner='], [$id]);
        $count['numcomments'] = CommentsModel::fetchColumn('count(id)', ['user='], [$id]);
        $count['numforumposts'] = PostsModel::fetchColumn('count(id)', ['userid='], [$id]);
        $count['numhnr'] = SnatchedModel::fetchColumn('COUNT(`hnr`)', ['uid='], [$id]);

        $html['userratio'] = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---";
        $html['avatar'] = htmlspecialchars($user["avatar"]) ? htmlspecialchars($user["avatar"]) : URLROOT . "/assets/images/misc/default_avatar.webp";
        $html['usersignature'] = stripslashes($user["signature"]);
        $html['country'] = CountriesModel::fetchRow($user['country']);

        // Init Data
        $data = [
            'dropdown' => $dropdown,
            'count' => $count,
            'user' => $user,
            'html' => $html
        ];
        return $data;
    }

    // Edit Profile Form Submit
    public static function submit($id, $data)
    {

        if (!empty($_POST)) {
            // Init Data
            $data = [
                'privacy' => Validate::Input('privacy'),
                'notifs' => Validate::Input('pmnotif') == 'yes' ? "[pm]" : "",
                'stylesheet' => Validate::Input('stylesheet'),
                'client' => Validate::Input('client'),
                'age' => Validate::Input('age', 'int'),
                'gender' => Validate::Input('gender'),
                'country' => Validate::Input('country'),
                'avatar' => Validate::Input('avatar'),
                'title' => Validate::Input('title'),
                'hideshoutbox' => Validate::Input('hideshoutbox') == 'yes' ? 'yes' : 'no',
                'tzoffset' => Validate::Input('tzoffset', 'int'),
                'signature' => Validate::Input('signature')
            ];

            if ($_POST['resetpasskey']) {
                $rand = array_sum(explode(" ", microtime()));
                $passkey = md5($data['current_user']->username . $rand . $data['current_user']->secret . ($rand * mt_rand()));
                $data['passkey'] = $passkey;
            }

            // Update
            DB::updateset("users", $data, ['id' => $id]);
            showMessage(("profile/$id"), LANG["UPDATE_COMPLETE"]);
        }
    }

    // Admin Edit Profile Form Submit
    public static function adminSubmited($id, $current_user)
    {
        $email = Validate::Input('email', 'email');

        // Check User
        if ($current_user->class < 5 && $id != $current_user->id) {
            showMessage(("profile/$id"), LANG["NO_PERMISSION"]);
        }

        // Init Data
        $data = [
            'downloaded' => strtobytes(Validate::Input('downloaded')),
            'uploaded' => strtobytes(Validate::Input('uploaded')),
            'ip' => Validate::Input('ip'),
            'class' => Validate::Input('class'),
            'donated' => Validate::Input('donated'),
            'password' => Validate::Input('password'),
            'warned' => Validate::Input('warned'),
            'downloadbanned' => Validate::Input('downloadbanned'),
            'shoutboxpos' => Validate::Input('shoutboxpos'),
            'modcomment' => Validate::Input('modcomment'),
            'enabled' => Validate::Input('enabled'),
            'invites' => Validate::Input('invites'),
            'email' => $email,
            'seedbonus' => Validate::Input('bonus'),
        ];

        // Change User Class
        if ($data['class'] != 0 && $data['class'] != $current_user->class) {
            // change user class
            $arr = DB::fetchRow('users', 'class', ['id ='], [$id]);
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $data['class'] && $uc > $current_user->class) {
                showMessage(URLROOT, LANG["YOU_CANT_DEMOTE_YOURSELF"]);
            } elseif ($uc == $current_user->class) {
                showMessage(URLROOT, LANG["YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"]);
            } else {
                DB::update('users', ['class'], ['id='], [$data['class'], $id]);
                // Notify user
                $prodemoted = ($data['class'] > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . GroupsModel::GetUserClassName($data['class'], $current_user) . " by " . $current_user->username . "";
                MessagesModel::insert(['sender' => 0, 'receiver' => $id, 'added' => CONFIG['TT_DATE'], 'subject' => 'Group Change', 'msg' => $msg]);
            }
        }

        // Reset Passkey Check
        if ($_POST['resetpasskey'] == 'yes') {
            $rand = array_sum(explode(" ", microtime()));
            $passkey = md5($current_user->username . $rand . $current_user->secret . ($rand * mt_rand()));
            $data = ['passkey' => $passkey];
        }

        // Change Password
        $chgpasswd = Validate::Input('chgpasswd') == 'yes' ? true : false;
        if ($chgpasswd) {
            $passres = DB::fetchRow('users', 'password', ['id ='], [$id]);
            if ($data['password'] != $passres['password']) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $data = ['password' => $password];
                LogsModel::write($current_user->username . " has changed password for user: $id");
            }
        }

        // Update
        DB::updateset("users", $data, ['id' => $id]);
        LogsModel::write($current_user->username . " has edited user: $id details");
        showMessage(("profile/$id"), LANG["UPDATE_COMPLETE"]);
    }

    // Delete Profile Default Page otdo (tidy up)
    public static function deleteprofile($userid, $current_user)
    {
        // Check User Input
        $delreason = Validate::Input('delreason');
        $sure = Validate::Input('sure', 'int');

        // Check User
        if (!$current_user->delete_users) {
            showMessage(("profile/$userid"), LANG["NO_PERMISSION"]);
        }

        if (!intval($userid)) {
            showMessage(("profile/$userid"), LANG["MISSING_ID"]);
        }

        if ($current_user->id == $userid) {
            showMessage(("profile/$userid"), LANG["YOU_CANT_DELETE_ADMIN"]);
        }

        if (!$delreason) {
            showMessage(("profile/$userid"), LANG["MISSING_FORM_DATA"]);
        }

        // Delete Profile
        UsersModel::deleteUser($userid);
        LogsModel::write($current_user->username . " has deleted account: " . coloredname($userid) . "");
        showMessage(("profile/$userid"), LANG["DEL_COMPLETE"]);
    }

}