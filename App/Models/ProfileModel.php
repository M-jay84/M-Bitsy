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
    public static function getProfile($USER, $id)
    {
        $user = DB::fetchRow('users', '*', ['id='], [$id]);

        if (!$user) {
            Redirect(Url(), Txt("NO_USER") . " $id.");
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && !$USER->permission('edit_users')) {
            Redirect(Url(), Txt("NO_ACCESS_ACCOUNT_DISABLED"));
        }

        // Start Blocked Users
        $show = FriendsModel::fetchRow('id', ['userid=','friend=','friendid='], [$user['id'],'enemy',$USER->has('id')]);

        if ($show && $USER->has('control_panel') != "yes") {
            Redirect(Url(), ("USER_PRIVATE"));
        }

        $dropdown['stylesheets'] = StylesheetsModel::getStyleDropDown($user['stylesheet']);
        $dropdown['countries'] = CountriesModel::dropdown($user['country']);
        $dropdown['tz'] = timeZoneDropDown($user['tzoffset']);
        $dropdown['teams'] = TeamsModel::dropDownTeams($user['team']);
        $dropdown['gender'] = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . Txt("MALE") . "</option>\n"
            . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . Txt("FEMALE") . "</option>\n";

        $count['numtorrents'] = TorrentsModel::fetchColumn('count(id)', ['owner='], [$id]);
        $count['numcomments'] = CommentsModel::fetchColumn('count(id)', ['user='], [$id]);
        $count['numforumposts'] = PostsModel::fetchColumn('count(id)', ['userid='], [$id]);
        $count['numhnr'] = SnatchedModel::fetchColumn('COUNT(`hnr`)', ['uid='], [$id]);

        $html['userratio'] = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---";
        $html['avatar'] = htmlspecialchars($user["avatar"]) ? htmlspecialchars($user["avatar"]) : URLROOT . "/assets/images/misc/default_avatar.webp";
        $html['usersignature'] = stripslashes($user["signature"]);
        $html['team'] = TeamsModel::getTeam($user['team']);
        $html['country'] = CountriesModel::fetchRow($user['country']);
        $friend = FriendsModel::countFriendAndEnemy($USER->has('id'), $id);

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
    public static function submit($USER, $id)
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
                $passkey = md5($USER->has('username') . $rand . $USER->has('secret') . ($rand * mt_rand()));
                $data['passkey'] = $passkey;
            }

            // Update
            UsersModel::updateset($data, $id);
            Redirect(Url("profile/$id"), Txt("UPDATE_COMPLETE"));
        }
    }

    // Admin Edit Profile Form Submit
    public static function adminSubmited($USER, $id)
    {
        $email = Input('email', 'email');

        // Check User
        if ($USER->has('class') < 5 && $id != $USER->has('id')) {
            Redirect(Url("profile/$id"), Txt("NO_PERMISSION"));
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
        if ($data['class'] != 0 && $data['class'] != $USER->has('class')) {
            // change user class
            $arr = UsersModel::fetchRow('class', ['id ='], [$id]);
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $data['class'] && $uc > $USER->has('class')) {
                Redirect(Url(), Txt("YOU_CANT_DEMOTE_YOURSELF"));
            } elseif ($uc == $USER->has('class')) {
                Redirect(Url(), Txt("YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"));
            } else {
                UsersModel::update(['class'], ['id='], [$data['class'], $id]);
                // Notify user
                $prodemoted = ($data['class'] > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . GroupsModel::GetUserClassName($data['class']) . " by " . $USER->has('username') . "";
                MessagesModel::insert(['sender' => 0, 'receiver' => $id, 'added' => get_date_time(), 'subject' => 'Group Change', 'msg' => $msg]);
            }
        }

        // Reset Passkey Check
        if ($_POST['resetpasskey'] == 'yes') {
            $rand = array_sum(explode(" ", microtime()));
            $passkey = md5($USER->has('username') . $rand . $USER->has('secret') . ($rand * mt_rand()));
            $data = ['passkey' => $passkey];
        }

        // Change Password
        $chgpasswd = Input('chgpasswd') == 'yes' ? true : false;
        if ($chgpasswd) {
            $passres = UsersModel::fetchRow('password', ['id ='], [$id]);
            if ($data['password'] != $passres['password']) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $data = ['password' => $password];
                LogsModel::write($USER->has('username') . " has changed password for user: $id");
            }
        }

        // Update
        UsersModel::updateset($data, $id);
        LogsModel::write($USER->has('username') . " has edited user: $id details");
        Redirect(Url("profile/$id"), Txt("UPDATE_COMPLETE"));
    }

    // Delete Profile Default Page otdo (tidy up)
    public static function deleteprofile($USER)
    {
        // Check User Input
        $userid = Input('userid', 'int');
        $delreason = Input('delreason', 'int');
        $sure = Input('sure', 'int');

        // Check User
        if (!$USER->has('delete_users')) {
            Redirect(Url("profile/$userid"), Txt("NO_PERMISSION"));
        }

        if (!Id($userid)) {
            Redirect(Url("profile/$userid"), Txt("MISSING_ID"));
        }

        if ($USER->has('id') == $userid) {
            Redirect(Url("profile/$userid"), Txt("YOU_CANT_DELETE_ADMIN"));
        }

        if (!$delreason) {
            Redirect(Url("profile/$userid"), Txt("MISSING_FORM_DATA"));
        }

        if ($sure == "0") {
            Redirect(Url('profile/delete'), "Sanity check: You are about to delete user. Click <a href='" . URLROOT . "/profile/delete?sure=1'>here</a> if you are sure.");
        }

        // Delete Profile
        UsersModel::deleteUser($userid);
        LogsModel::write($USER->has('username') . " has deleted account: " . coloredname($userid) . "");
        Redirect(Url("profile/$userid"), Txt("DEL_COMPLETE"));
    }

}