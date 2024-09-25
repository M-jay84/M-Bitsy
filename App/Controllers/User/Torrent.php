<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Libs\SimpleThumbnail;
use App\Libs\Validate;
use App\Models\LogsLogic;
use App\Models\MessagesLogic;
use App\Models\TorrentsLogic;
use App\Models\TorrentTagLogic;
use PDO;

/*
* Torrent Controller
*/
class Torrent extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Torrent Default Page // TODO
    public function index($id, $action = '')
    {
        $torrent = TorrentsLogic::fetchIdDetails('torrents.id=?', [$id], $this->current_user);

        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY'] && $torrent['owner'] != $id) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }

        // Add Hit
        if ($action == 'hit')
        {
            $pvkey_var = "t_" . $id;
            $views = $_SESSION[$pvkey_var] == 1 ? $torrent["views"] + 0 : $torrent["views"] + 1;
            $_SESSION[$pvkey_var] = 1;
            $torrents_query = "UPDATE `torrents` SET views = ? WHERE id=?";
            DB::run($torrents_query, [$views, $id]); 
            $this->redirect("torrent/$id");
        }

        // Add Bump
        if ($action == 'bump') {
            $torrents_query = "UPDATE `torrents` SET added = ? WHERE id=?";
            DB::run($torrents_query, [CONFIG['TT_DATE'], $id]);
            showMessage("torrent/$id", LANG["ACTION_COMPLETE"]);
        }

        // Data For View
        $data = [
            'page_title' => LANG["TORRENT_DETAILS_FOR"] . " " . $torrent['name'] . "",
            'torrent' => $torrent['torrent'],
            'tmdb1' => $torrent['tmdb1'],
            'image' => $torrent['image'],
            'video' => $torrent['video'],
            'likes' => $torrent['likes'],
            'V_thanks' => $torrent['thanks'],
            'tags' => $torrent['tags'],
            'html' => $torrent['html'],
            'scraper' => $torrent['scraper'],
            'current_user' => $this->current_user
        ];

        // Load View
        $this->template('user-torrent', $data, 'user');
    }

    // Edit Torrent Form Submit // todo 
    public function submit($id)
    {
    
        // Get Torrent Data
        $torrents_query = "SELECT owner,image2,image1 FROM `torrents` WHERE id=? ";
        $row = DB::run($torrents_query, [$id])->fetch(PDO::FETCH_ASSOC);

        if ($this->current_user->control_panel == "no" && $this->current_user->id != $id) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }

        $updateset = array();

        // If Input Update
        if (!empty($_POST)) {
            // NFO
            $nfoaction = Validate::Input('nfoaction');
            if ($nfoaction == "update") {
                $nfofile = $_FILES['nfofile'];
                if (!$nfofile) {
                    die("No data " . var_dump($_FILES));
                }
                if ($nfofile['size'] > 65535) {
                    showMessage("torrent/$id", "NFO is too big! Max 65,535 bytes.");
                }
                $nfofilename = $nfofile['tmp_name'];
                if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0) {
                    @move_uploaded_file($nfofilename, UPLOAD_PATH . "/nfos/$id.nfo");
                    $updateset['nfo'] = 'yes';
                } //success
            } elseif ($nfoaction == "delete") {
                unlink(UPLOAD_PATH . "/nfos/$id");
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
                $torrenttag_query = "INSERT INTO `torrenttag` (torrentid, type1, type2, type3) VALUES (?,?,?,?)";
                DB::run($torrenttag_query, [$id, $tags[0], $tags[1], $tags[2]]);
            }

            // Images
            $img1action = Validate::Input('img1action');
            if ($img1action == "update") {
                $updateset['image1'] = SimpleThumbnail::create()->uploadimage(0, $row["image1"], $id);
            }

            if ($img1action == "delete") {
                if ($row['image1']) {
                    unlink(UPLOAD_PATH . "/images/$row[image1]");
                    $updateset['image1'] = '';
                }
            }

            $img2action = Validate::Input('img2action');
            if ($img2action == "update") {
                $updateset['image2'] = SimpleThumbnail::create()->uploadimage(1, $row["image2"], $id);
            }

            if ($img2action == "delete") {
                if ($row['image2']) {
                    unlink(UPLOAD_PATH . "/images/$row[image2]");
                    $updateset['image2'] = '';
                }
            }

            // Update
            TorrentsLogic::updateset($updateset, $id);
            LogsLogic::write("Torrent $id (" . htmlspecialchars($_POST["name"]) . ") was edited by " . $this->current_user->username . "");
            showMessage("torrent/$id", LANG['SUCCESS']);
        } else {
            showMessage("torrent/$id", LANG['ACTION_FAILED']);
        }
    }

    // Delete Torrent Default Page
    public function deleteok()
    {
        // Check User Input
        $torrentid = Validate::Input('id', 'id');
        $delreason = Validate::Input('delreason');
        $torrentname = Validate::Input('torrentname');

        // Check Corrert Input
        if (!$this->current_user->delete_torrents == 'yes') {
            showMessage("torrent/$torrentid", LANG["NO__PERMISSION"]);
        }
        if (!intval($torrentid)) {
            showMessage("torrent/delete?id=$torrentid", LANG["MISSING_ID"]);
        }
        if (!$delreason) {
            showMessage("torrent/delete?id=$torrentid", LANG["MISSING_FORM_DATA"]);
        }

        // Delete Torrent
        $torrent_query = "SELECT name,owner FROM `torrents` WHERE id=?";
        $owner = DB::run($torrent_query, [$torrentid])->fetch(PDO::FETCH_ASSOC);
        TorrentsLogic::deleteTorrent($torrentid, $this->current_user->id);
        LogsLogic::write($this->current_user->username . " has deleted torrent: ID:$torrentid - " . htmlspecialchars($torrentname) . " - Reason: " . htmlspecialchars($delreason));
        unlink(UPLOAD_PATH . "/torrents/$torrentid.torrent");

        if ($this->current_user->id=== $owner['owner']) {
            $msg_shout = "Your torrent " . $torrentname . " has been deleted by " . $this->current_user->username . "/nReason: " . $_POST["delreason"] . "";
            MessagesLogic::newinsert(['sender' => 0, 'receiver' => $owner['owner'], 'added' => CONFIG['TT_DATE'], 'subject' => 'Torrent Deleted', 'msg' => $msg_shout, 'unread' => 'yes', 'location' => 'in']);
        }

        showMessage("peer/seeding/$owner[owner]", htmlspecialchars($torrentname) . " " . LANG["DEL_ERROR"]);
    }
}