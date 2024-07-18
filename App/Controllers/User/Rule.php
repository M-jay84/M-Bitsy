<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\RulesModel;

/*
* Rules Controller
*/
class Rule 
{
    // Rules Page
    public function index()
    {
        $USER = new User(0, 1);
        // Get Rules
        $rules = RulesModel::gettAll();

        // Init Data
        $data = [
            'V_title' => Txt('RULE'),
            'V_rules' => $rules,
            'USER' => $USER
        ];

        // Load View
        template('rule', $data, 'user');
    }

    // Edit/Add Rule
    public function edit()
    {
        $USER = new User(0, 1);
        if (!$USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            RulesModel::insertOrUpdate();
        }
    }

    // Delete Rule
    public function delete($id)
    {
        $USER = new User(0, 1);
        if (!$USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            RulesModel::delete($id);
        }
    }
}