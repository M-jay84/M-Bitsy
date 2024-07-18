<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Torrent Controller
*/
class Admintorrent 
{
    // Torrents Default Page
    public function index($type = '') // todo like %?%
    {
        $USER = new User(_MODERATOR, 2);
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

        // Init Data
        $data = [
            'V_title' => Txt("TORRENT"),
            'V_count'=> $torrents['count'],
            'V_pager' => $torrents['pager'],
            'V_torrents' =>$torrents['stmt'],
            'V_search' => $search,
            'USER' => $USER
        ];

        // Load View
        template('torrent', $data, 'admin');
    }

    // Torrents Delete
    public function delete()
    {
        $USER = new User(_MODERATOR, 2);
        if (!count($_POST["ids"])) {
            Redirect(Url('admintorrent'), Txt("NOTHING_SELECTED"));
        }
        foreach ($_POST["ids"] as $id) {
            TorrentsModel::deleteTorrent(intval($id));
        }
        Redirect(Url('admintorrent'), Txt("DEL_COMPLETE"));
    }

}