<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\TorrentLangModel;

/*
* Language Controller
*/
class Admintorrentlang 
{
    // Torrent Language Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            TorrentLangModel::insertOrUpdate();
          } else {
            // Get Torrent Lang Data
            $torrentlang = TorrentLangModel::fetchAll();
    
            // Init Data
            $data = [
                'V_title' => Txt("LANGUAGE"),
                'V_torrentlang' => $torrentlang
            ];

            // Load View
            template('torrentlang', $data, 'admin');
          }
    }

    // Delete Torrent Language Default Page
    public function delete($id, $sure = '')
    {
        $USER = new User(_MODERATOR, 2);
        TorrentLangModel::update($id, $sure);
    }

}