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
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Search Default Page
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        $torrent = TorrentsModel::countSearch();

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
        $this->view('user-search', $data, 'user');
    }

}