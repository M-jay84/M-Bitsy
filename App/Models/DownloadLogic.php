<?php

namespace App\Models;

use App\Libs\DB;

/*
* Download Logic
*/
class DownloadLogic
{
    // Download Torrent
    public static function torrent($id, $passkey)
    {
        // Get User
        $member = DB::fetchColumn('users', 'count(id)', ['passkey ='], [$passkey]);
        
        if ($member == 1) {
            $user = array();
            $visitor = DB::run("SELECT users.id, users.class, users.downloadbanned, users.role_id FROM `users` 
            LEFT OUTER JOIN `groups` 
            ON users.class=groups.group_id 
            WHERE passkey = ?",
            [$passkey]
            )->fetch(\PDO::FETCH_ASSOC);;
            foreach ($visitor as $key => $value) {
                $user[$key] = $value;
            }
            $role = $user['role_id'] == 1 ? $user['group_role_id'] : $user['role_id'];
            $permission = PermissionsLogic::fetchRow('can_download, view_torrents', ['permission_id ='], [$role]);
            foreach ($permission as $key => $value) {
                $user[$key] = $value;
            }
        }
            
        if (CONFIG['MEMBERSONLY']) {
            if (!$user) {
                die(LANG["MEMBERS_ONLY"]);
            } elseif ($user["can_download"] == "no") {
                showMessage(URLROOT, LANG["NO_PERMISSION_TO_DOWNLOAD"]);
            } elseif ($user["downloadbanned"] == "yes") {
                showMessage(URLROOT, LANG["DOWNLOADBAN"]);
            }
        } elseif ($user['passkey'] == '') {
            $rand = array_sum(explode(" ", microtime()));
            // todo passkey
            $guestpasskey = md5('username' . $rand . uniqid(20) . ($rand * mt_rand()));
            $passkey = $guestpasskey;
        }
            
        // Get Torrent
        $torrent = TorrentsLogic::fetchRow('filename, banned, external, announce, owner, vip', ['id ='], [$id]);
        if (!$torrent) {
            showMessage(URLROOT, LANG["TORRENT_NOT_FOUND"]);
        } elseif ($torrent["banned"] == "yes") {
            showMessage(URLROOT, LANG["BANNED_TORRENT"]);
        } elseif ($user['class'] < CONFIG['_VIP'] && $torrent['vip'] == "yes") {
            showMessage(URLROOT, LANG["VIPTODOWNLOAD"]);
        } elseif ($user['view_torrents'] != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }
            
        // Thanks
        if (CONFIG['FORCETHANKS'] && CONFIG['MEMBERSONLY']) {
            if ($user["id"] != $torrent["owner"]) {
                $like = DB::fetchRow('likes', ['liked =', 'type =', 'user ='], [$id, 'torrent', $user['id']]);
                if (!$like) {
                    showMessage(URLROOT, LANG["PLEASE_THANK"]);
                }
            }
        }

        // Check The File
        $fn = UPLOAD_PATH . "/torrents/$id.torrent";
        if (!is_file($fn)) {
            showMessage(UPLOAD_PATH . "/torrents/$id.torrent", LANG["FILE_NOT_FILE"]);
        }
        if (!is_readable($fn)) {
            showMessage(UPLOAD_PATH . "/torrents/$id.torrent", LANG["FILE_UNREADABLE"]);
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
        TorrentsLogic::update(['hits'], ['id='], [$views,$id]);

        $data = [
            'file' => $fn,
            'external' => $torrent["external"],
            'name' => $name
        ];

        return $data;
    }

}