<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\CommentsLogic;
use App\Models\CountriesLogic;
use App\Models\GroupsLogic;
use App\Models\LogsLogic;
use App\Models\MessagesLogic;
use App\Models\PostsLogic;
use App\Models\SnatchedLogic;
use App\Models\TorrentsLogic;
use PDO;

/*
* Users Table Data
*/
class ProfileLogic
{
    // Get User Profile
    public static function getProfile($id, $current_user)
    {
        $users_query = "SELECT * FROM `users` WHERE id = ?";
        $user = DB::run($users_query, [$id])->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            showMessage(URLROOT, LANG["NO_USER"] . " $id.");
        }

        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && !$current_user->edit_users == 'yes') {
            showMessage(URLROOT, LANG["NO_ACCESS_ACCOUNT_DISABLED"]);
        }

        $dropdown['countries'] = CountriesLogic::dropdown($user['country']);
        $dropdown['tz'] = timeZoneDropDown($user['tzoffset']);
        $dropdown['gender'] = "<option value='Male'" . ($user['gender'] == "Male" ? " selected='selected'" : "") . ">" . LANG["MALE"] . "</option>\n"
            . "<option value='Female'" . ($user['gender'] == "Female" ? " selected='selected'" : "") . ">" . LANG["FEMALE"] . "</option>\n";

        $torrents_query = "SELECT COUNT(`id`) FROM `torrents` WHERE owner=?";
        $count['numtorrents'] = DB::run($torrents_query, [$id])->fetchColumn();
        $comments_query = "SELECT COUNT(`id`) FROM `comments` WHERE user=?";
        $count['numcomments'] = DB::run($comments_query, [$id])->fetchColumn();
        $posts_query = "SELECT COUNT(`id`) FROM `forum_posts` WHERE userid=?";
        $count['numforumposts'] = DB::run($posts_query, [$id])->fetchColumn();
        $snatched_query = "SELECT COUNT(`hnr`) FROM `snatched` WHERE uid=?";
        $count['numhnr'] = DB::run($snatched_query, [$id])->fetchColumn();

        $html['userratio'] = $user["downloaded"] > 0 ? number_format($user["uploaded"] / $user["downloaded"], 1) : "---";
        $html['avatar'] = htmlspecialchars($user["avatar"]) ? htmlspecialchars($user["avatar"]) : URLROOT . "/assets/images/misc/default_avatar.webp";
        $html['usersignature'] = stripslashes($user["signature"]);
        $html['country'] = CountriesLogic::fetchRow($user['country']);

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
            $users_query = "SELECT class FROM `users` WHERE id = ?";
            $arr = DB::run($users_query, [$id])->fetch(PDO::FETCH_ASSOC);
            $uc = $arr['class'];
            // skip if class is same as current
            if ($uc != $data['class'] && $uc > $current_user->class) {
                showMessage(URLROOT, LANG["YOU_CANT_DEMOTE_YOURSELF"]);
            } elseif ($uc == $current_user->class) {
                showMessage(URLROOT, LANG["YOU_CANT_DEMOTE_SOMEONE_SAME_LVL"]);
            } else {
                $users_query = "UPDATE users SET class = ? WHERE id=?";
                DB::run($users_query, [$data['class'], $id]);
                // Notify user
                $prodemoted = ($data['class'] > $uc ? "promoted" : "demoted");
                $msg = "You have been $prodemoted to " . GroupsLogic::GetUserClassName($data['class'], $current_user) . " by " . $current_user->username . "";
                MessagesLogic::newinsert(['sender' => 0, 'receiver' => $id, 'added' => CONFIG['TT_DATE'], 'subject' => 'Group Change', 'msg' => $msg]);
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
            $users_query = "SELECT password FROM `users` WHERE id = ?";
            $passres = DB::run($users_query, [$id])->fetch(PDO::FETCH_ASSOC);
            if ($data['password'] != $passres['password']) {
                $password = password_hash($data['password'], PASSWORD_BCRYPT);
                $data = ['password' => $password];
                LogsLogic::write($current_user->username . " has changed password for user: $id");
            }
        }

        // Update
        DB::updateset("users", $data, ['id' => $id]);
        LogsLogic::write($current_user->username . " has edited user: $id details");
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
        UsersLogic::deleteUser($userid);
        LogsLogic::write($current_user->username . " has deleted account: " . coloredname($userid) . "");
        showMessage(("profile/$userid"), LANG["DEL_COMPLETE"]);
    }

}