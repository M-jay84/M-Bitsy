<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\TorrentsModel;
use App\Models\LogsModel;
use App\Models\MessagesModel;
use App\Models\TorrentTagModel;

/*
* Torrent Controller
*/
class Torrent extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Torrent Default Page // TODO
    public function index($id, $action = '')
    {
    
        $torrent = (new TorrentsModel)->fetchIdDetails('torrents.id=?', [$id], $this->current_user);

        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY'] && $torrent['owner'] != $id) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }

        // Add Hit
        if ($action == 'hit') {
            $pvkey_var = "t_" . $id;
            $views = $_SESSION[$pvkey_var] == 1 ? $torrent["views"] + 0 : $torrent["views"] + 1;
            $_SESSION[$pvkey_var] = 1;
            (new TorrentsModel)->update(['views'], ['id='], [$views, $id]);
            Url(Url("torrent/$id"));
        }

        // Add Bump
        if ($action == 'bump') {
            (new TorrentsModel)->update(['added'], ['id='], [get_date_time(), $id]);
            showMessage(Url("torrent/$id"), LANG["ACTION_COMPLETE"]);
        }

        // Data For View
        $data = [
            'page_title' => LANG["TORRENT_DETAILS_FOR"] . " " . $torrent['name'] . "",
            'V_torrent' => $torrent['torrent'],
            'V_tmdb1' => $torrent['tmdb1'],
            'V_image' => $torrent['image'],
            'V_video' => $torrent['video'],
            'V_likes' => $torrent['likes'],
            'V_thanks' => $torrent['thanks'],
            'V_tags' => $torrent['tags'],
            'V_html' => $torrent['html'],
            'V_scraper' => $torrent['scraper'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->view('torrent', $data, 'user');
    }

    // Edit Torrent Form Submit // todo 
    public function submit($id)
    {
    
        // Get Torrent Data
        $row = (new TorrentsModel)->fetchRow('owner,image2,image1', ['id ='], [$id]);

        if ($this->current_user->control_panel == "no" && $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
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
                    showMessage(Url("torrent/$id"), "NFO is too big! Max 65,535 bytes.");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, CONFIG['UPLOADDIR'] . "/nfos/$id.nfo");
                    $updateset['nfo'] = 'yes';
                } //success
            } elseif ($nfoaction == "delete") {
                unlink(CONFIG['UPLOADDIR'] . "/nfos/$id");
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

            if ($this->current_user->class > CONFIG['_MODERATOR']) {
                $updateset['sticky'] = $_POST["sticky"] == 'yes' ? 'yes' : 'no';
            }

            if ($this->current_user->edit_torrents == 'yes') {
                $updateset['freeleech'] = $_POST["freeleech"] ? 1 : 0;
            }

            if ($this->current_user->edit_torrents == 'yes') {
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
                (new TorrentTagModel)->insert(['torrentid' => $id, 'type1' => $tags[0], 'type2' => $tags[1], 'type3' => $tags[2]]);
            }

            // Images
            $img1action = Input('img1action');
            if ($img1action == "update") {
                $updateset['image1'] = uploadimage(0, $row["image1"], $id);
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    unlink(CONFIG['UPLOADDIR'] . "/images/$row[image1]");
                    $updateset['image1'] = '';
                }
            }

            $img2action = Input('img2action');
            if ($img2action == "update") {
                $updateset['image2'] = uploadimage(1, $row["image2"], $id);
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    unlink(CONFIG['UPLOADDIR'] . "/images/$row[image2]");
                    $updateset['image2'] = '';
                }
            }

            // Update
            (new TorrentsModel)->updateset($updateset, $id);
            (new LogsModel)->write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by " . $this->current_user->username . "");
            showMessage(Url("torrent/$id"), LANG['SUCCESS']);
        } else {
            showMessage(Url("torrent/$id"), LANG['ACTION_FAILED']);
        }
    }

    // Delete Torrent Default Page
    public function deleteok()
    {
    
        // Check User Input
        $torrentid = Input('id', 'id');
        $delreason = Input('delreason');
        $torrentname = Input('torrentname');

        // Check Corrert Input
        if (!$this->current_user->delete_torrents == 'yes') {
            showMessage(Url("torrent/$torrentid"), LANG["NO__PERMISSION"]);
        }
        if (!Id($torrentid)) {
            showMessage(Url("torrent/delete?id=$torrentid"), LANG["MISSING_ID"]);
        }
        if (!$delreason) {
            showMessage(Url("torrent/delete?id=$torrentid"), LANG["MISSING_FORM_DATA"]);
        }

        // Delete Torrent
        $owner = (new TorrentsModel)->fetchRow('name, owner', ['id ='], $torrentid);
        (new TorrentsModel)->deleteTorrent($torrentid, $this->current_user->id);
        (new LogsModel)->write($this->current_user->username . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
        unlink(CONFIG['UPLOADDIR'] . "/torrents/$torrentid.torrent");

        if ($this->current_user->id=== $owner['owner']) {
            $msg_shout = "Your torrent " . $torrentname . " has been deleted by " . $this->current_user->username . "/nReason: " . $_POST["delreason"] . "";
            MessagesModel::insert(['sender' => 0, 'receiver' => $owner['owner'], 'added' => get_date_time(), 'subject' => 'Torrent Deleted', 'msg' => $msg_shout, 'unread' => 'yes', 'location' => 'in']);
        }

        showMessage(Url("peer/seeding/$owner[owner]"), htmlspecialchars($torrentname) . " " . LANG["DEL_ERROR"]);
    }
}