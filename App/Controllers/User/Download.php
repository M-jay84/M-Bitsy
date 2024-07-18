<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Libs\Bencode;
use App\Models\PermissionsModel;
use App\Models\LikesModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;

/*
* Download Controller
*/
class Download 
{
    // Download Torrent  todo (tidy up)
    public function index($id, $key = '')
    {
        $USER = new User(0, 0);
        $passkey = $key == "" ?  $USER->has('passkey') : $key;

        // Get User
        $member = UsersModel::fetchColumn('count(id)', ['passkey ='], [$passkey]);
        if ($member == 1) {
            $user = array();
            $test = UsersModel::downloadUser($passkey);
            foreach ($test as $key => $value) {
                $user[$key] = $value;
            }
            $role = $user['role_id'] == 1 ? $user['group_role_id'] : $user['role_id'];
            $permission = PermissionsModel::fetchRow('can_download, view_torrents', ['permission_id ='], [$role]);
            foreach ($permission as $key => $value) {
                $user[$key] = $value;
            }
        }

        if (CONFIG['MEMBERSONLY']) {
            if (!$user) {
                Redirect($_SERVER['HTTP_REFERER'], Txt("MEMBERS_ONLY"));
            } elseif ($user["can_download"] == "no") {
                Redirect(Url(), Txt("NO_PERMISSION_TO_DOWNLOAD"));
            } elseif ($user["downloadbanned"] == "yes") {
                Redirect(Url(), Txt("DOWNLOADBAN"));
            }
        } elseif ($user['passkey'] == '') {
            $rand = array_sum(explode(" ", microtime()));
            $guestpasskey = md5($USER->has('username') . $rand . uniqid(20) . ($rand * mt_rand()));
            $passkey = $guestpasskey;
        }

        // Get Torrent
        $torrent = TorrentsModel::fetchRow('filename, banned, external, announce, owner, vip', ['id ='], [$id]);
        if (!$torrent) {
            Redirect(Url(), Txt("TORRENT_NOT_FOUND"));
        } elseif ($torrent["banned"] == "yes") {
            Redirect(Url(), Txt("BANNED_TORRENT"));
        } elseif ($user['class'] < _VIP && $torrent['vip'] == "yes") {
            Redirect(Url(), Txt("VIPTODOWNLOAD"));
        } elseif (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }

        // Thanks
        if (CONFIG['FORCETHANKS'] && CONFIG['MEMBERSONLY']) {
            if ($user["id"] != $torrent["owner"]) {
                $like = LikesModel::fetchRow(['liked =', 'type =', 'user ='], [$id, 'torrent', $user['id']]);
                if (!$like) {
                    Redirect(Url(), Txt("PLEASE_THANK"));
                }
            }
        }

        // Check The File
        $fn = UPLOADDIR . "/torrents/$id.torrent";
        if (!is_file($fn)) {
            Redirect(UPLOADDIR . "/torrents/$id.torrent", Txt("FILE_NOT_FILE"));
        }
        if (!is_readable($fn)) {
            Redirect(UPLOADDIR . "/torrents/$id.torrent", Txt("FILE_UNREADABLE"));
        }

        // Name Download File
        $name = $torrent['filename'];
        $friendlyurl = str_replace("http://", "", URLROOT);
        $friendlyname = str_replace(".torrent", "", $name);
        $friendlyext = ".torrent";
        $name = $friendlyname . "[" . $friendlyurl . "]" . $friendlyext;

        // Update Hit When Downloaded
        $pvkey_var = "d_" . $id;
        $views = $_SESSION[$pvkey_var] == 1 ? $torrent["hits"] + 0 : $torrent["hits"] + 1;
        $_SESSION[$pvkey_var] = 1;
        TorrentsModel::update(['hits'], ['id='], [$views,$id]);

        // Local Torrent To Add Passkey
        if ($torrent["external"] != 'yes') {
            // Bencode
            $dict = Bencode::decode(file_get_contents($fn));
            $dict['announce'] = sprintf(PASSKEYURL, $passkey);
            unset($dict['announce-list']);
            $data = Bencode::encode($dict);
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header("Content-Type: application/x-bittorrent");
            print $data;
        } else {
            // Download External
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Length: ' . filesize($fn));
            header("Content-Type: application/x-bittorrent");
            readfile($fn);
        }
    }
}