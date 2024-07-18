<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Need Seed Controller
*/
class Needseed 
{
    // Search Need Seed Default Page
    public function index()
    {
        $USER = new User(0, 1);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }

        // Get Torrent Data
        $torrents =  TorrentsModel::getAll("WHERE torrents.banned = ? AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders", ['no']);
        if ($torrents['count'] == 0) {
            Redirect(Url(), Txt("NOTHING_FOUND"));
        }

        // Init Data
        $data = [
            'V_title' => Txt("TORRENT_NEED_SEED"),
            'V_torrents' => $torrents['stmt'],
            'USER' => $USER
        ];

        // Load View
        template('needseed', $data, 'user');
    }

}