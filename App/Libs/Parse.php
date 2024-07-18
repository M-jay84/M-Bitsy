<?php

namespace App\Libs;

use App\Libs\TorrentFile;

/*
* Parse Data From Torrent File
*/
class Parse
{

    // Get Data From Torrent File
    public function torr($path = "", $url = URLROOT)
    {
        // First Check File Is Torrent
        $torrent = explode(".", $path);
        $fileend = strtolower(end($torrent));
        if ($fileend != "torrent") {
            Redirect($url, Txt('NOT_TORRENT'));
        }

        // Set Array
        $torrentInfo = array();

        // Load Torrent
        try {
            $torrent = TorrentFile::load($path);
        } catch (\Exception $e) {
            Redirect($url, Txt("ERROR_TORRENT"));
        }

        // Get Data
        $torrentInfo['announce'] = $torrent->getAnnounce();
        $torrentInfo["announce-list"] = $torrent->getAnnounceList();
        $torrentInfo["creation date"] = date('r', $torrent->getCreationDate());
        $torrentInfo['comment'] = $torrent->getComment();
        $torrentInfo['name'] = $torrent->getName();
        $torrentInfo['protocol'] = $torrent->getProtocol(); // V1,V2,HYBRID

        $hash = $torrent->getInfoHashs();
        if ($torrentInfo['protocol'] == 'v2') {
            $torrentInfo['hash'] = substr($hash['v2'], 0, 40);
            $torrentInfo['hash2'] = null;
        } elseif ($torrentInfo['protocol'] == 'v1') {
            $torrentInfo['hash'] = $hash['v1'];
            $torrentInfo['hash2'] = null;
        } elseif ($torrentInfo['protocol'] == 'hybrid') {
            $torrentInfo['hash'] = $hash['v1'];
            $torrentInfo['hash2'] = substr($hash['v2'], 0, 40);
        }

        // Get File Data
        $parse = $torrent->parse();
        $torrentInfo['files'] = serialize($parse['fileTree']);
        $torrentInfo['length'] = $parse['total_size'];
        $torrentInfo['ttfilecount'] = $parse['count'];

        return $torrentInfo;
    }

}