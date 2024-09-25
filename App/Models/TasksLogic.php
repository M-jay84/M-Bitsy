<?php

namespace App\Models;

use App\Libs\DB;

/*
* Task Table Data
*/
class TasksLogic
{
    // Insert Task
    public static function clearshoutbox($sure = '')
    {
        ShoutboxLogic::truncate($sure);
	}
	
}