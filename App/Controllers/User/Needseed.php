<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Need Seed Controller
*/
class Needseed extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(1);
    }

    // Search Need Seed Default Page
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }

        // Get Torrent Data
        $torrents =  TorrentsModel::getAll("WHERE torrents.banned = ? AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders", ['no']);
        if ($torrents['count'] == 0) {
            showMessage(Url(), LANG["NOTHING_FOUND"]);
        }

        // Data For View
        $data = [
            'page_title' => LANG["TORRENT_NEED_SEED"],
            'V_torrents' => $torrents['stmt']
        ];

        // Load View
        $this->view('needseed', $data, 'user');
    }

}