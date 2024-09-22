<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\RulesLogic;

/*
* Rules Controller
*/
class Rule extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Rules
    public function index()
    {
        // Data For View
        $data = [
            'TITLE' => LANG['RULE'],
            'RULES' => RulesLogic::gettAll(),
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('user-rule', $data, 'user');
    }

    // Edit/Add Rule
    public function edit()
    {
        if (!$this->current_user->isStaff) {
            showMessage('rule', LANG["NO_PERMISSION"]);
        } else {
            if (!empty($_POST)) {
                RulesLogic::insertOrUpdate($this->current_user->username);
            } else {
                showMessage('rule', LANG["NOTHING_SELECTED"]);
            }
        }
    }

    // Delete Rule
    public function delete($id)
    {
        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        } else {
            RulesLogic::delete($id, $this->current_user->username);
        }
    }
}