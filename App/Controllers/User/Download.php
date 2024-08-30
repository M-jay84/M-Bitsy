<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\Bencode;
use App\Models\PermissionsModel;
use App\Models\LikesModel;
use App\Models\TorrentsModel;
use App\Models\UsersModel;
use PSpell\Config;

/*
* Download Controller
*/
class Download extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(0);
    }

    // Download Torrent  todo (tidy up)
    public function index($id, $key = '')
    {
        $passkey = $key == "" ?  $this->current_user->passkey : $key;

        // Get User
        $member = UsersModel::fetchColumn('count(id)', ['passkey ='], [$passkey]);
        if ($member == 1) {
            $user = array();
            $test = UsersModel::downloadUser($passkey);
            foreach ($test as $key => $value) {
                $user[$key] = $value;
            }
            $role = $user['role_id'] == 1 ? $user['group_role_id'] : $user['role_id'];
            $permission = (new PermissionsModel)->fetchRow('can_download, view_torrents', ['permission_id ='], [$role]);
            foreach ($permission as $key => $value) {
                $user[$key] = $value;
            }
        }

        if (CONFIG['MEMBERSONLY']) {
            if (!$user) {
                showMessage(Url($_SERVER['HTTP_REFERER']), LANG["MEMBERS_ONLY"]);
            } elseif ($user["can_download"] == "no") {
                showMessage(Url(), LANG["NO_PERMISSION_TO_DOWNLOAD"]);
            } elseif ($user["downloadbanned"] == "yes") {
                showMessage(Url(), LANG["DOWNLOADBAN"]);
            }
        } elseif ($user['passkey'] == '') {
            $rand = array_sum(explode(" ", microtime()));
            $guestpasskey = md5($this->current_user->username . $rand . uniqid(20) . ($rand * mt_rand()));
            $passkey = $guestpasskey;
        }

        // Get Torrent
        $torrent = (new TorrentsModel)->fetchRow('filename, banned, external, announce, owner, vip', ['id ='], [$id]);
        if (!$torrent) {
            showMessage(Url(), LANG["TORRENT_NOT_FOUND"]);
        } elseif ($torrent["banned"] == "yes") {
            showMessage(Url(), LANG["BANNED_TORRENT"]);
        } elseif ($user['class'] < CONFIG['_VIP'] && $torrent['vip'] == "yes") {
            showMessage(Url(), LANG["VIPTODOWNLOAD"]);
        } elseif ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }

        // Thanks
        if (CONFIG['FORCETHANKS'] && CONFIG['MEMBERSONLY']) {
            if ($user["id"] != $torrent["owner"]) {
                $like = (new LikesModel)->fetchRow(['liked =', 'type =', 'user ='], [$id, 'torrent', $user['id']]);
                if (!$like) {
                    showMessage(Url(), LANG["PLEASE_THANK"]);
                }
            }
        }

        // Check The File
        $fn = CONFIG['UPLOADDIR'] . "/torrents/$id.torrent";
        if (!is_file($fn)) {
            showMessage(Url(CONFIG['UPLOADDIR'] . "/torrents/$id.torrent"), LANG["FILE_NOT_FILE"]);
        }
        if (!is_readable($fn)) {
            showMessage(Url(CONFIG['UPLOADDIR'] . "/torrents/$id.torrent"), LANG["FILE_UNREADABLE"]);
        }

        // Name Download File
        $name = $torrent['filename'];
        $friendlyurl = str_replace("http://", "", CONFIG['URLROOT']);
        $friendlyname = str_replace(".torrent", "", $name);
        $friendlyext = ".torrent";
        $name = $friendlyname . "[" . $friendlyurl . "]" . $friendlyext;

        // Update Hit When Downloaded
        $pvkey_var = "d_" . $id;
        $views = $_SESSION[$pvkey_var] == 1 ? $torrent["hits"] + 0 : $torrent["hits"] + 1;
        $_SESSION[$pvkey_var] = 1;
        (new TorrentsModel)->update(['hits'], ['id='], [$views,$id]);

        // Local Torrent To Add Passkey
        if ($torrent["external"] != 'yes') {
            // Bencode
            $dict = Bencode::decode(file_get_contents($fn));
            $dict['announce'] = sprintf(CONFIG['URLROOT'] . CONFIG['PASSKEYURL'], $passkey);
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