<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\ReportsModel;

/*
* Report Controller
*/
class Report extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Report Submit Page
    public function index()
    {
        if (!empty($_POST)) {
            ReportsModel::reportItem($this->current_user);
        } else {
            $this->showMessage(URLROOT, LANG["NOTHING_SELECTED"]);
        }
    }

}