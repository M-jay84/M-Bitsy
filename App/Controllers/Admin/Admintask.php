<?php

namespace App\Controllers\Admin;

use App\Libs\Cache;
use App\Libs\Cleanup;
use App\Libs\Controller;
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
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // No Default Page
    public function index()
    {
        Url(Url('admincp'), true);
    }

    // Cleanup
    public function cleanup()
    {
    
        (new TasksModel)->update(['last_time'], ['task='], [gmtime(),'cleanup']);
        Cleanup::run();
        showMessage(Url('admincp'), LANG["FORCE_CLEAN_COMPLETED"]);
    }

    // Prune Cache
    public function cache()
    {
    
        $TTCache = new Cache();
        $TTCache->Delete("sql_cache/blocks_left");
        $TTCache->Delete("sql_cache/blocks_middle");
        $TTCache->Delete("sql_cache/blocks_right");
        $TTCache->Delete("sql_cache/latestuploadsblock");
        $TTCache->Delete("sql_cache/mostactivetorrents_block");
        $TTCache->Delete("sql_cache/newestmember_block");
        $TTCache->Delete("sql_cache/seedwanted_block");
        $TTCache->Delete("sql_cache/usersonline_block");
        $TTCache->Delete("sql_cache/request_block");
        showMessage(Url('admincp'), LANG['PURGE_COMPLETED']);
    }

}