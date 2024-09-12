<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Torrents\Bencode;
use App\Models\DownloadModel;
use App\Models\PermissionsModel;
use App\Models\LikesModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

/*
* Download Controller
*/
class Download extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();
    }

    // Download Torrent  todo (tidy up)
    public function index($id, $key = '')
    {
        $passkey = $key == "" ?  $this->current_user->passkey : $key;
        
        $data = DownloadModel::torrent($id, $passkey);

        // Local Torrent To Add Passkey
        if ($data["external"] != 'yes') {
            // Bencode
            $dict = Bencode::decode(file_get_contents($data['file']));
            $dict['announce'] = sprintf(URLROOT . CONFIG['PASSKEYURL'], $passkey);
            unset($dict['announce-list']);
            $data = Bencode::encode($dict);
            header('Content-Disposition: attachment; filename="' . $data['name'] . '"');
            header("Content-Type: application/x-bittorrent");
            print $data;
        } else {
            // Download External
            header('Content-Disposition: attachment; filename="' . $data['name'] . '"');
            header('Content-Length: ' . filesize($data['file']));
            header("Content-Type: application/x-bittorrent");
            readfile($data['file']);
        }
    }
}