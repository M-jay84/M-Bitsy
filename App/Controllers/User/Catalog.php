<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Catalog Controller
*/
class Catalog 
{
    // Catalog Default Page
    public function index()
    {
        $USER = new User(0, 2);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        } else {
            $array = torrentsearch('torrents.name');
            $where = !$array ? '' : "$array[query] AND";
    
            // Pagination
            $torrents = TorrentsModel::getAll("WHERE $where visible != ?", array('no'), "catalog$array[url]");
    
            // Init Data
            $data = [
                'V_title' => Txt("CATALOGUE"),
                'V_torrents' => $torrents['stmt'],
                'V_pager' => $torrents['pager'],
                'USER' => $USER
            ];
    
            // Load View
            template('catalog', $data, 'user');
        }
    }
    
}