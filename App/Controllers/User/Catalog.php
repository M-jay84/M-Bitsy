<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Catalog Controller
*/
class Catalog extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Catalog Default Page
    public function index()
    {
    
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        } else {
            $array = torrentsearch('torrents.name');
            $where = !$array ? '' : "$array[query] AND";
    
            // Pagination
            $torrents = TorrentsModel::getAll("WHERE $where visible != ?", array('no'), "catalog$array[url]");
    
            // Data For View
            $data = [
                'page_title' => LANG["CATALOGUE"],
                'V_torrents' => $torrents['stmt'],
                'V_pager' => $torrents['pager'],
            'current_user' => $this->current_user
            ];
    
            // Load View
            $this->view('catalog', $data, 'user');
        }
    }
    
}