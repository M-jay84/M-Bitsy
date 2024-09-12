<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\BonusModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Bonus Page
    public function index()
    {
        $data = [
            'page_title' => LANG['BONUS'],
            'bonus_result' => BonusModel::getAll(),
            'set_bonus_interval' => BonusModel::interval(),
            'current_user' => $this->current_user
        ];

        $this->view('user-bonus', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            BonusModel::getById($this->current_user);
        } else {
            $this->showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }
    
    // Bonus Edit/Add Form Submit
    public function update()
    {
        if ($this->current_user->isStaff) {
            if (!empty($_POST)) {
                BonusModel::insertOrUpdate();
            } else {
                $this->showMessage('bonus', LANG["NOTHING_FOUND"]);
            }
        } else {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        } 
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }

    // Delete Bonus By Link $id
    public function delete($id)
    {
        if ($this->current_user->isStaff) {
            BonusModel::delete($id);
        } else {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }

}