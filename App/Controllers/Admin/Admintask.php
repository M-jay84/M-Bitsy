<?php

namespace App\Controllers\Admin;

use App\Libs\Cache;
use App\Libs\Cleanup;
use App\Libs\User;
use App\Models\TasksModel;

/*
* Task Controller
*/
class Admintask 
{
    // No Default Page
    public function index()
    {
        Redirect(Url('admincp'));
    }

    // Cleanup
    public function cleanup()
    {
        $USER = new User(_MODERATOR, 2);
        TasksModel::update(['last_time'], ['task='], [gmtime(),'cleanup']);
        Cleanup::run();
        Redirect(Url('admincp'), Txt("FORCE_CLEAN_COMPLETED"));
    }

    // Prune Cache
    public function cache()
    {
        $USER = new User(_MODERATOR, 2);
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
        Redirect(Url('admincp'), Txt('PURGE_COMPLETED'));
    }

}