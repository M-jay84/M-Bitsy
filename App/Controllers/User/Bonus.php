<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
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
        showMessage('bonus', LANG["SUCCESS"]);
    }
    
    // Bonus Edit/Add Form Submit
    public function update()
    {
        if ($this->current_user->isStaff) {
            if (!empty($_POST)) {
                BonusLogic::insertOrUpdate();
            } else {
                showMessage('bonus', LANG["NOTHING_FOUND"]);
            }
        } else {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        } 
        showMessage('bonus', LANG["SUCCESS"]);
    }

    // Delete Bonus By Link $id
    public function delete($id)
    {
        if ($this->current_user->isStaff) {
            $bonus_query = "DELETE FROM `bonus` WHERE id' = ?";
            DB::run($bonus_query, [$id]);
        } else {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        showMessage('bonus', LANG["SUCCESS"]);
    }

}