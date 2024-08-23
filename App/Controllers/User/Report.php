<?php

namespace App\Controllers\User;

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
        $this->current_user = $this->currentuser(2);
    }

    // Report Submit Page
    public function index()
    {
        (new ReportsModel)->reportItem($this->current_user);
    }

}