<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\GroupsLogic;

/*
* Users Search
*/
class UsersearchLogic
{
    // Admin Confirm User Reg
    public static function searchUsers()
    {
        // Get Search Data
        $data = [
            "name" => Validate::Input('name'),
            "ratio" => Validate::Input('ratio'),
            "member" => Validate::Input('member'),
            "email" => Validate::Input('email'),
            "ip" => Validate::Input('ip') ?? $_GET['ip'],
            "account" => Validate::Input('account'),
            "uploaded" => Validate::Input('uploaded'),
            "class" => Validate::Input('class'),
            "downloaded" => Validate::Input('downloaded'),
            "warned" => Validate::Input('warned')
        ];

        
        $url = "?"; // assign url
        $wherea = []; // assign conditions
        $params = []; // assign vars

        if (!empty($data['name'])) {
            $wherea[] = "users.username LIKE '%$data[name]%'";
        }

        if (!empty($data['ratio'])) {
            $ratio = $data['ratio'];
            $wherea[] = "users.uploaded/users.downloaded > $ratio";
        }

        if (!empty($data['member'])) {
            $wherea[] = "users.status = '$data[member]'";
        }

        if (!empty($data['email'])) {
            $wherea[] = "users.email LIKE '%$data[email]%'";
        }

        if (!empty($data['ip'])) {
            $wherea[] = "users.ip LIKE '%$data[ip]%'";
        }

        if (!empty($data['account'])) {
            $wherea[] = "users.enabled = '$data[account]'";
        }

        if (!empty($data['uploaded'])) {
            $uploaded = strtobytes($data['uploaded']);
            $wherea[] = "users.uploaded > $uploaded";
        }

        if (!empty($data['class'])) {
            $wherea[] = "users.class = '$data[class]'";
        }

        if (!empty($data['downloaded'])) {
            $downloaded = strtobytes($data['downloaded']);
            $wherea[] = "users.downloaded > $downloaded";
        }

        if (!empty($data['warned'])) {
            $wherea[] = "users.warned = '$data[warned]'";
        }

        $where = implode(' AND ', $wherea);
        if ($where != '') {
            $where = 'WHERE ' . $where;
        }

        // Pagination
        $count = DB::run("SELECT COUNT(`id`) FROM `users` $where")->fetchcolumn();
        list($pager, $limit) = pagination(25, $count, URLROOT . "/adminusersearch&");
        $sql = DB::run("SELECT * FROM `users` $where $limit")->fetchAll();
        $results = [
            'results' => $sql,
            'pager' => $pager,
            'inputs' => $data
        ];
        $groups = GroupsLogic::fetchAll('group_id,groups.level', '');

        $array = [
            'results' => $sql,
            'pager' => $pager,
            'inputs' => $data,
            'groups' => $groups
        ];

        return $array;
    }

    // Get Members
    public static function submitUser($current_user)
    {
        $enable = Validate::Input('enable');
        $disable = Validate::Input('disable');
        $unwarn = Validate::Input('unwarn');
        $warn = Validate::Input('warn');
        $add_comment = Validate::Input('add_comment');
        $ids = Validate::multiIds('ids');

        if ($disable != '') {
            DB::updateWhereIn('users', ['enabled' => 'no'], $ids);
        }

        if ($enable != '') {
            DB::updateWhereIn('users', ['enabled' => 'yes'], $ids);
        }

        if ($unwarn != '') {
            foreach ($_POST["ids"] as $user_id) {
                MessagesLogic::insert(['poster' => 0, 'sender' => 0, 'subject' => 'Warning Removed', 'receiver' => $user_id, 'added' => CONFIG['TT_DATE'], 'msg' => "Your Warning Has Been Removed"]);
            }
            $modcomment = date("Y-m-d") . " - Warning Removed By " . $current_user->username;
            DB::updateWhereIn('users', ['modcomment' => $modcomment], $ids);
            DB::updateWhereIn('users', ['warned' => 'no'], $ids);
        }

        if ($warn != '') {
            if (empty($add_comment)) {
                showMessage('adminusersearch', LANG["MISSING_FORM_DATA"], 1);
            }
            foreach ($_POST["ids"] as $user_id) {
                MessagesLogic::insert(['poster' => 0, 'sender' => 0, 'subject' => 'New Warning', 'receiver' => $user_id, 'added' => CONFIG['TT_DATE'], 'msg' => "You have received a warning, Reason: $add_comment"]);
            }
            $modcomment = date("Y-m-d") . " - Warned by " . $current_user->username . ".\nReason: $add_comment";
            DB::updateWhereIn('users', ['modcomment' => $modcomment], $ids);
            DB::updateWhereIn('users', ['warned' => 'yes'], $ids);
        }

    }
}