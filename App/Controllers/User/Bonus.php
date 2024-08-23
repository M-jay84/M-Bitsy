<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\BonusModel;

/*
* Bonus Controller
*/
class Bonus extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if (!$this->current_user->loggedin) {
            $this->showMessage(Url('login'), true);
        }
    }

    // Bonus Page
    public function index()
    {
        $data = [
            'page_title' => LANG['BONUS'],
            'bonus_result' => (new BonusModel)->getAll(),
            'set_bonus_interval' => floor(CONFIG['ADDBONUS'] / 60),
            'current_user' => $this->current_user
        ];

        $this->view('bonus', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            (new BonusModel)->getById($this->current_user);
        } else {
            $this->showMessage(Url('bonus'), LANG["NOTHING_FOUND"]);
        }
        $this->showMessage(Url('bonus'), LANG["SUCCESS"]);
    }
    
    // Bonus Edit/Add Form Submit
    public function update()
    {
        if (!$this->current_user->isStaff) {
            $this->showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            if (!empty($_POST)) {
                (new BonusModel)->insertOrUpdate();
            } else {
                $this->showMessage(Url('bonus'), LANG["NOTHING_FOUND"]);
            }
        } 
        $this->showMessage(Url('bonus'), LANG["SUCCESS"]);
    }

    // Delete Bonus By Link
    public function delete($id)
    {
        if (!$this->current_user->isStaff) {
            $this->showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            (new BonusModel)->delete($id);
        }
        $this->showMessage(Url('bonus'), LANG["DEL_COMPLETE"]);
    }

}