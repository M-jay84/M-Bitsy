<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Reseed Controller
*/
class Reseed 
{
    // Request Reseed Form Submit todo (tidy up)
    public function index($id)
    {
        $USER = new User(0, 1);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }
        TorrentsModel::reseed($id);
    }

}