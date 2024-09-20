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
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Torrents
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
            'TITLE' => LANG["TORRENT"],
            'COUNT'=> $torrents['count'],
            'PAGER' => $torrents['pager'],
            'TORRENTS' =>$torrents['stmt'],
            'TYPE' => $type,
            'SEARCH' => $search
        ];

        // Load View
        $this->view('admin-torrent', $data, 'admin');
    }

    // Torrents Delete
    public function delete()
    {
        if (!count($_POST["ids"])) {
            showMessage('admintorrent', LANG["NOTHING_SELECTED"]);
        }
        foreach ($_POST["ids"] as $id) {
            TorrentsModel::deleteTorrent(intval($id), $this->current_user->id);
        }
        showMessage('admintorrent', LANG["DEL_COMPLETE"]);
    }

}