<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Search Controller
*/
class Search 
{
    // Search Default Page
    public function index()
    {
        $USER = new User(0, 1);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }
        $torrent = TorrentsModel::countSearch();

        // Init Data
        $data = [
            'V_title' => $torrent['title'],
            'V_torrents' => $torrent['torrents'],
            'V_pager' => $torrent['pager'],
            'V_keyword' => $torrent['search']['keyword'],
            'V_url' => $torrent['search']['url'],
            'V_cats' => $torrent['cats'],
            'V_lang' => $torrent['lang'],
            'USER' => $USER
        ];
        
        // Load View
        template('search', $data, 'user');
    }

}