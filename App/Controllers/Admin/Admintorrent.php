<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Torrent Controller
*/
class Admintorrent extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Torrents Default Page
    public function index($type = '') // todo like %?%
    {
    
        // Check User Input
        $search = (!empty($_GET["search"])) ? htmlspecialchars(trim($_GET["search"])) : "";
        $pattern = '%' . $search . '%';
        $where = ($search == "") ? "" : "AND torrents.name LIKE $pattern";

        if ($type == 'dead') {
            $torrents = TorrentsModel::getAll("WHERE banned = ? AND seeders < 1 ORDER BY torrents.added DESC", array('no'), 'admintorrent/dead');
        } elseif ($type == 'free') {
            $torrents = TorrentsModel::getAll("WHERE freeleech=? $where ORDER BY name", array(1), 'admintorrent/free');
        } elseif ($type == 'banned') {
            $torrents = TorrentsModel::getAll("WHERE banned=? $where ORDER BY name", array('yes'), 'admintorrent/banned');
        } else {
            $torrents = TorrentsModel::getAll("WHERE torrents.id > ? $where ORDER BY name", array(0), 'admintorrent?');
        }

        // Data For View
        $data = [
            'page_title' => LANG["TORRENT"],
            'V_count'=> $torrents['count'],
            'V_pager' => $torrents['pager'],
            'V_torrents' =>$torrents['stmt'],
            'V_search' => $search
        ];

        // Load View
        $this->view('torrent', $data, 'admin');
    }

    // Torrents Delete
    public function delete()
    {
    
        if (!count($_POST["ids"])) {
            showMessage(Url('admintorrent'), LANG["NOTHING_SELECTED"]);
        }
        foreach ($_POST["ids"] as $id) {
            (new TorrentsModel)->deleteTorrent(intval($id), $this->current_user->id);
        }
        showMessage(Url('admintorrent'), LANG["DEL_COMPLETE"]);
    }

}