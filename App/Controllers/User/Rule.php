<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\RulesModel;

/*
* Rules Controller
*/
class Rule extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(1);
    }

    // Rules Page
    public function index()
    {
        // Get Rules
        $rules = (new RulesModel)->gettAll();

        // Data For View
        $data = [
            'page_title' => LANG['RULE'],
            'V_rules' => $rules,
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('rule', $data, 'user');
    }

    // Edit/Add Rule
    public function edit()
    {
        if (!$this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new RulesModel)->insertOrUpdate($this->current_user->username);
        }
    }

    // Delete Rule
    public function delete($id)
    {
        if (!$this->current_user->class > CONFIG['_MODERATOR']) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new RulesModel)->delete($id, $this->current_user->username);
        }
    }
}