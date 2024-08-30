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
    public $bonus;
    public $current_user;

    // Get Current User Object
    public function __construct()
    {
        $this->bonus = new BonusModel();
        $this->current_user = (new Auth)->user(2);

        if (!$this->current_user->loggedin) {
            $this->redirect('home');
        }
    }

    // Bonus Page
    public function index()
    {
        $data = [
            'page_title' => LANG['BONUS'],
            'bonus_result' => $this->bonus->getAll(),
            'set_bonus_interval' => $this->bonus->interval(),
            'current_user' => $this->current_user
        ];

        $this->view('bonus', $data, 'user');
    }

    // Bonus Form Submit
    public function submit()
    {
        if (Inputexsist('post')) {
            $this->bonus->getById($this->current_user);
        } else {
            $this->showMessage('bonus', LANG["NOTHING_FOUND"]);
        }
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }
    
    // Bonus Edit/Add Form Submit
    public function update()
    {
        if ($this->current_user->isStaff) {
            if (Inputexsist('post')) {
                $this->bonus->insertOrUpdate();
            } else {
                $this->showMessage('bonus', LANG["NOTHING_FOUND"]);
            }
        } else {
            $this->showMessage('home', LANG["NO_PERMISSION"]);
        } 
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }

    // Delete Bonus By Link $id
    public function delete($id)
    {
        if ($this->current_user->isStaff) {
            $this->bonus->delete($id);
        } else {
            $this->showMessage('home', LANG["NO_PERMISSION"]);
        }
        $this->showMessage('bonus', LANG["SUCCESS"]);
    }

}