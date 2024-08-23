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
        $TTCache->Delete("block/blocks_left");
        $TTCache->Delete("block/blocks_middle");
        $TTCache->Delete("block/blocks_right");
        $TTCache->Delete("block/latestuploadsblock");
        $TTCache->Delete("block/mostactivetorrents_block");
        $TTCache->Delete("block/newestmember_block");
        $TTCache->Delete("block/seedwanted_block");
        $TTCache->Delete("block/usersonline_block");
        $TTCache->Delete("block/request_block");
        showMessage(Url('admincp'), LANG['PURGE_COMPLETED']);
    }

}