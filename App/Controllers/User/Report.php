<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\ReportsModel;

/*
* Report Controller
*/
class Report 
{
    // Report Submit Page
    public function index()
    {
        $USER = new User(0, 2);
        ReportsModel::reportItem($USER);
    }

}