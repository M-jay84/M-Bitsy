<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Search Controller
*/
class Search extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(1);
    }

    // Search Default Page
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }
        $torrent = (new TorrentsModel)->countSearch();

        // Data For View
        $data = [
            'page_title' => $torrent['title'],
            'V_torrents' => $torrent['torrents'],
            'V_pager' => $torrent['pager'],
            'V_keyword' => $torrent['search']['keyword'],
            'V_url' => $torrent['search']['url'],
            'V_cats' => $torrent['cats'],
            'V_lang' => $torrent['lang']
        ];
        
        // Load View
        $this->view('search', $data, 'user');
    }

}