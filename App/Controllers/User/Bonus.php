<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\BonusModel;

/*
* Bonus Controller
*/
class Bonus 
{
    // Bonus Page
    public function index()
    {
        $USER = new User(0, 2);
        // Get Data
        $bonus = BonusModel::getAll();

        // Init Data
        $data = [
            'V_title' => Txt('BONUS'),
            'V_bonus' => $bonus,
            'V_interval' => floor(CONFIG['ADDBONUS'] / 60),
            'USER' => $USER
        ];

        // Load View
        template('bonus', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        $USER = new User(0, 2);
        if (!empty($_POST)) {
            BonusModel::getById($USER);
        } else {
            Redirect(Url('bonus'), Txt("NOTHING_FOUND"));
        }
    }
    
    // Bonus Edit/Add Default Page
    public function update()
    {
        $USER = new User(0, 2);
        if (!$USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            BonusModel::insertOrUpdate();
        } 
    }

    // Delete Bonus
    public function delete($id, $sure = '')
    {
        $USER = new User(0, 2);
        if (!$USER->isStaff()) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            BonusModel::delete($id, $sure);
        }
    }

}