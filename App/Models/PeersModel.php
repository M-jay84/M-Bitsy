<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\TorrentsModel;

/*
* Peers Table Data
*/
class PeersModel
{
    // Fetch Peer Column
    public static function fetchColumn($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('peers', $columns, $where, $values, $other)->fetchColumn();
		return $stmt;
	}

    // Get Peers
    public static function getPeers($USER, $type, $id, $pagination = false)
    {
        if ($USER->permission('view_users', $id) && $USER->has('class') < _UPLOADER && $type == ('seeding' || 'leeching')) {
            Redirect(Url(), Txt("NO_USER_VIEW"));
        }

        $title = Txt("USER_DETAILS_FOR") . coloredname($id);

        if ($type == 'torrent') {
            if ($USER->has('torrents') == "no" && $USER->has('id') != $id && $USER->has('class') < _UPLOADER) {
                Redirect(Url(), Txt("NO_TORRENT_VIEW"));
            }
            $where = 'peers.torrent = ?';
            $values = [$id];
            $name = TorrentsModel::fetchRow('name', ['id ='], [$id]);
            $title = Txt("TORRENT_DETAILS_FOR") . $name;
        } elseif ($type == 'admin') {
            $where = 'torrents.id > ?';
            $values = [0];
        }  elseif ($type == 'seeding') {
            $where = 'peers.userid = ? AND peers.seeder =  ?';
            $values = [$id, 'yes'];
        } elseif ($type == 'leeching') {
            $where = 'peers.userid = ? AND peers.seeder =  ?';
            $values = [$id, 'no'];
        }

        $count = DB::run(
            "SELECT COUNT(*) FROM peers 
             LEFT JOIN torrents ON peers.torrent = torrents.id 
             LEFT JOIN categories ON torrents.category = categories.id 
             WHERE $where", $values
        )->fetchColumn();

        list($pager, $limit) = pagination(25, $count, URLROOT . "/$pagination&");

        $stmt = DB::run(
            "SELECT peers.id as pid, peers.torrent, peers.peer_id, peers.ip, peers.port, peers.uploaded, peers.downloaded,
                   peers.to_go, peers.seeder, peers.started, peers.last_action, peers.connectable, peers.client, peers.userid, peers.passkey,
                   torrents.info_hash, torrents.name, torrents.size, torrents.visible,  torrents.owner, torrents.freeleech, torrents.vip, 
                   torrents.seeders, torrents.leechers, torrents.category,
                   categories.name AS cat_name,  categories.parent_cat as cat_parent
            FROM peers 
            LEFT JOIN torrents ON peers.torrent = torrents.id 
            LEFT JOIN categories ON torrents.category = categories.id 
            WHERE $where $limit", $values
        )->fetchAll();

        $array = [
            'stmt' => $stmt,
            'pager' => $pager,
            'count' => $count,
            'title' => $title
        ];
    
        return $array;
    }
}