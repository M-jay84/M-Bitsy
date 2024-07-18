<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;
use App\Models\LogsModel;
use App\Models\MessagesModel;
use App\Models\TorrentTagModel;

/*
* Torrent Controller
*/
class Torrent
{
    // Torrent Default Page
    public function index($id, $action = '')
    {
        $USER = new User(_MODERATOR, 2);
        $torrent = TorrentsModel::fetchIdDetails('torrents.id=?', [$id]);

        if (!$USER->permission('view_torrents') && $torrent['owner'] != $id) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }

        // Add Hit
        if ($action == 'hit') {
            $pvkey_var = "t_" . $id;
            $views = $_SESSION[$pvkey_var] == 1 ? $torrent["views"] + 0 : $torrent["views"] + 1;
            $_SESSION[$pvkey_var] = 1;
            TorrentsModel::update(['views'], ['id='], [$views, $id]);
            Redirect(Url("torrent/$id"));
        }

        // Add Bump
        if ($action == 'bump') {
            TorrentsModel::update(['added'], ['id='], [get_date_time(), $id]);
            Redirect(Url("torrent/$id"), Txt("ACTION_COMPLETE"));
        }

        // Init Data
        $data = [
            'V_title' => Txt("TORRENT_DETAILS_FOR") . " " . $torrent['name'] . "",
            'V_torrent' => $torrent['torrent'],
            'V_tmdb1' => $torrent['tmdb1'],
            'V_image' => $torrent['image'],
            'V_video' => $torrent['video'],
            'V_likes' => $torrent['likes'],
            'V_thanks' => $torrent['thanks'],
            'V_tags' => $torrent['tags'],
            'V_html' => $torrent['html'],
            'V_scraper' => $torrent['scraper'],
            'USER' => $USER
        ];

        // Load View
        template('torrent', $data, 'user');
    }

    // Edit Torrent Form Submit // todo 
    public function submit($id)
    {
        $USER = new User(_MODERATOR, 2);
        // Get Torrent Data
        $row = TorrentsModel::fetchRow('owner,image2,image1', ['id ='], [$id]);

        if ($USER->has('control_panel') == "no" && $USER->has('id') != $id) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        }

        $updateset = array();

        // If Input Update
        if (!empty($_POST)) {
            // NFO
            $nfoaction = Input('nfoaction');
            if ($nfoaction == "update") {
                $nfofile = $_FILES['nfofile'];
                if (!$nfofile) {
                    die("No data " . var_dump($_FILES));
                }
                if ($nfofile['size'] > 65535) {
                    Redirect(Url("torrent/$id"), "NFO is too big! Max 65,535 bytes.");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, UPLOADDIR . "/nfos/$id.nfo");
                    $updateset['nfo'] = 'yes';
                } //success
            } elseif ($nfoaction == "delete") {
                unlink(UPLOADDIR . "/nfos/$id");
                $updateset['nfo'] = 'no';
            }

            if (!empty($_POST["name"])) {
                $updateset['name'] = $_POST["name"];
            }

            $updateset['tmdb'] = $_POST["tmdb"];
            $updateset['imdb'] = $_POST["imdb"];
            $updateset['descr'] = $_POST["descr"];
            $updateset['category'] = (int) $_POST["type"];
            $updateset['tube'] = $_POST['tube'] ?? '';
            $updateset['vip'] = $_POST["vip"] ? "yes" : "no";
            $updateset['anon'] = $_POST["anon"] ? "yes" : "no";
            $updateset['torrentlang'] = (int) $_POST["language"] ?? 0;

            if ($USER->isStaff()) {
                $updateset['sticky'] = $_POST["sticky"] == 'yes' ? 'yes' : 'no';
            }

            if ($USER->permission('edit_torrents')) {
                $updateset['freeleech'] = $_POST["freeleech"] ? 1 : 0;
            }

            if ($USER->permission('edit_torrents')) {
                if ($_POST["banned"]) {
                    $updateset['banned'] = 'yes';
                    $_POST['visible'] = 'no';
                } else {
                    $updateset['banned'] = 'no';
                }
            }

            $updateset['visible'] = $_POST["visible"] ? "yes" : "no";

            $tags = $_POST['tags'];
            if ($tags) {
                TorrentTagModel::insert(['torrentid' => $id, 'type1' => $tags[0], 'type2' => $tags[1], 'type3' => $tags[2]]);
            }

            // Images
            $img1action = Input('img1action');
            if ($img1action == "update") {
                $updateset['image1'] = uploadimage(0, $row["image1"], $id);
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    unlink(UPLOADDIR . "/images/$row[image1]");
                    $updateset['image1'] = '';
                }
            }

            $img2action = Input('img2action');
            if ($img2action == "update") {
                $updateset['image2'] = uploadimage(1, $row["image2"], $id);
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    unlink(UPLOADDIR . "/images/$row[image2]");
                    $updateset['image2'] = '';
                }
            }

            // Update
            TorrentsModel::updateset($updateset, $id);
            LogsModel::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by " . $USER->has('username') . "");
            Redirect(Url("torrent/$id"), Txt('SUCCESS'));
        } else {
            Redirect(Url("torrent/$id"), Txt('ACTION_FAILED'));
        }
    }

    // Delete Torrent Default Page
    public function deleteok()
    {
        $USER = new User(_MODERATOR, 2);
        // Check User Input
        $torrentid = Input('id', 'id');
        $delreason = Input('delreason');
        $torrentname = Input('torrentname');

        // Check Corrert Input
        if (!$USER->permission('delete_torrents')) {
            Redirect(Url("torrent/$torrentid"), Txt("NO__PERMISSION"));
        }
        if (!Id($torrentid)) {
            Redirect(Url("torrent/delete?id=$torrentid"), Txt("MISSING_ID"));
        }
        if (!$delreason) {
            Redirect(Url("torrent/delete?id=$torrentid"), Txt("MISSING_FORM_DATA"));
        }

        // Delete Torrent
        $owner = TorrentsModel::fetchRow('name, owner', ['id ='], $torrentid);
        TorrentsModel::deleteTorrent($torrentid);
        LogsModel::write($USER->has('username') . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
        unlink(UPLOADDIR . "/torrents/$torrentid.torrent");

        if ($USER->has('id')=== $owner['owner']) {
            $msg_shout = "Your torrent " . $torrentname . " has been deleted by " . $USER->has('username') . "/nReason: " . $_POST["delreason"] . "";
            MessagesModel::insert(['sender' => 0, 'receiver' => $owner['owner'], 'added' => get_date_time(), 'subject' => 'Torrent Deleted', 'msg' => $msg_shout, 'unread' => 'yes', 'location' => 'in']);
        }

        Redirect(Url("peer/seeding/$owner[owner]"), htmlspecialchars($torrentname) . " " . Txt("DEL_ERROR"));
    }
}