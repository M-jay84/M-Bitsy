<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Cache;
use App\Libs\Controller;
use App\Models\ShoutboxModel;
use App\Models\TasksModel;

/*
* Task Controller
*/
class Admintask extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    public function index(){}

    // Cleanup
    public function cleanup()
    {
        TasksModel::update(['last_time'], ['task='], [CONFIG['TT_TIME'],'cleanup']);
        Cleanup::run();
        $this->showMessage('admincp', LANG["FORCE_CLEAN_COMPLETED"]);
    }

    // Prune Cache
    public function cache()
    {
        $TTCache = new Cache();
        $TTCache->Delete("sql_cache/latestuploadsblock");
        $TTCache->Delete("sql_cache/mostactivetorrents_block");
        $TTCache->Delete("sql_cache/newestmember_block");
        $TTCache->Delete("sql_cache/usersonline_block");
        showMessage('admincp', LANG['PURGE_COMPLETED']);
    }
	
    // Clear Shoutbox
    public static function clearshoutbox($sure = '')
    {
        ShoutboxModel::truncate($sure);
	}
	
}