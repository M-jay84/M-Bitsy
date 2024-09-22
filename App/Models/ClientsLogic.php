<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;

/*
* Clients Table Data
*/
class ClientsLogic
{
    // Admin Add Client
    public static function insert()
    {
        $name = Validate::Input('type'); // todo type ???
        $peer_id = Validate::Input('peer_id');
        $peer = hex2bin($_POST['peer_id']);
        $code = substr($peer, 0, 8);
        $banned = 'yes';

        DB::insert('clients', ['agent_name'=>$name, 'hits'=>$banned, 'ins_date'=>CONFIG['TT_DATE'], 'code'=>$code, 'peer_id'=>$peer_id]);
        showMessage('adminclient', LANG["SUCCESS"]);

    }

    // Admin Get All
    public static function select($type)
    {
        $array = array(); 
        if ($type == 'clients') {
            // Get Client Data
            $clients = DB::fetchAll('clients', 'hits, agent_name, code, agent_id, peer_id', '');
            foreach ($clients as $client) {
                $array[] = ['id' => $client['agent_id'], 'type' => $client['agent_name'], 'peer_id' => $client['peer_id'], 'banned' => $client['hits'], 'connected' => 'no'];
            }
        } elseif ($type == 'peers') {
            // Get Peer Client Data
            $peers = DB::run("SELECT DISTINCT client FROM `peers`")->fetchAll();
            foreach ($peers as $peer) {
                $peerid = DB::run("SELECT peer_id FROM `peers` WHERE client = ?", [$peer['client']])->fetchColumn();
                $array[] = ['id' => 0, 'type' => $peer['client'], 'peer_id' => $peerid, 'banned' => 0, 'connected' => 'yes'];
            }
        }

        return $array;
    }

    // Admin Delete Client
    public static function delete($id)
    {
        DB::delete('clients', ['agent_id'=>$id]);
        showMessage('adminclient', LANG["SUCCESS"]);
    }

}