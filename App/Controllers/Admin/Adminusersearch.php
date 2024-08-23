<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\GroupsModel;
use App\Models\MessagesModel;
use App\Models\UsersModel;

/*
* Search User Controller
*/
class Adminusersearch extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    public function index()
    {
    
        $search = $_POST['action'] ?? '0';
        if ($search == '1') {
            self::submit();
        }

        $inputs = [
            "name" => Input('name'),
            "ratio" => Input('ratio'),
            "member" => Input('member'),
            "email" => Input('email'),
            "ip" => Input('ip') ?? $_GET['ip'],
            "account" => Input('account'),
            "uploaded" => Input('uploaded'),
            "class" => Input('class'),
            "downloaded" => Input('downloaded'),
            "warned" => Input('warned')
        ];

        // Get Search Data
        $results = UsersModel::adminUserSearch($inputs);
        $groups = (new GroupsModel)->fetchAll('group_id,groups.level', '');

        // Data For View
        $data = [
            'page_title' => LANG["USER_SEARCH"],
            'V_results' => $results['results'],
            'V_pager' => $results['pager'],
            'V_inputs' => $inputs,
            'V_groups' => $groups
        ];

        // Load View
        $this->view('user_search', $data, 'admin');
    }

    // Submit Form
    private function submit() // todo tidy up
    {
    
        if (!empty($_POST["ids"])) {
            $enable = Input('enable');
            $disable = Input('disable');
            $unwarn = Input('unwarn');
            $warn = Input('warn');
            $add_comment = Input('add_comment');
            $ids = multiIds('ids');

            if ($disable != '') {
                UsersModel::updateWhereIn(['enabled' => 'no'], $ids);
            }

            if ($enable != '') {
                UsersModel::updateWhereIn(['enabled' => 'yes'], $ids);
            }

            if ($unwarn != '') {
                foreach ($_POST["ids"] as $user_id) {
                    MessagesModel::insert(['poster' => 0, 'sender' => 0, 'subject' => 'Warning Removed', 'receiver' => $user_id, 'added' => get_date_time(), 'msg' => "Your Warning Has Been Removed"]);
                }
                $modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $this->current_user->username;
                UsersModel::updateWhereIn(['modcomment' => $modcomment], $ids);
                UsersModel::updateWhereIn(['warned' => 'no'], $ids);
            }

            if ($warn != '') {
                if (empty($add_comment)) {
                    showMessage(Url('adminusersearch'), LANG["MISSING_FORM_DATA"], 1);
                }
                foreach ($_POST["ids"] as $user_id) {
                    MessagesModel::insert(['poster' => 0, 'sender' => 0, 'subject' => 'New Warning', 'receiver' => $user_id, 'added' => get_date_time(), 'msg' => "You have received a warning, Reason: $add_comment"]);
                }
                $modcomment = gmdate("Y-m-d") . " - Warned by " . $this->current_user->username . ".\nReason: $add_comment";
                UsersModel::updateWhereIn(['modcomment' => $modcomment], $ids);
                UsersModel::updateWhereIn(['warned' => 'yes'], $ids);
            }

        } else {
            showMessage(Url('adminusersearch'), LANG["MISSING_ID"]);
        }
    }
    
}