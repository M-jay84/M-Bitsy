<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\BonusLogic;

/*
* Bonus Controller
*/
class Bonus extends Controller
{
    public $current_user;

    // Get Current User Object
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Bonus
    public function index()
    {
        $data = [
            'TITLE' => LANG['BONUS'],
            'BONUS' => BonusLogic::getAll(),
            'BONUS_INTERVAL' => BonusLogic::interval(),
            'USER' => $this->current_user
        ];

        $this->template('user-bonus', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            BonusLogic::getById($this->current_user);
        } else {
            showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
    }
    
    // Bonus Edit/Add Form Submit
    public function update()
    {
        if ($this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        if (!empty($_POST)) {
            BonusLogic::insertOrUpdate();
        } else {
            showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
    }

    // Delete Bonus By Link $id
    public function delete($id)
    {
        if ($this->current_user->isStaff) {
            BonusLogic::deleteone($id);
        } else {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

}