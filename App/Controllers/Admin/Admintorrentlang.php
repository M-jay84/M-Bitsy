<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\TorrentLangModel;

/*
* Language Controller
*/
class Admintorrentlang extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Torrent Language Default Page
    public function index()
    {
    
        if (!empty($_POST)) {
            (new TorrentLangModel)->insertOrUpdate();
          } else {
            // Get Torrent Lang Data
            $torrentlang = (new TorrentLangModel)->fetchAll();
    
            // Data For View
            $data = [
                'page_title' => LANG["LANGUAGE"],
                'V_torrentlang' => $torrentlang
            ];

            // Load View
            $this->view('torrentlang', $data, 'admin');
          }
    }

    // Delete Torrent Language Default Page
    public function delete($id, $sure = '')
    {
        (new TorrentLangModel)->update($id, $sure);
    }

}