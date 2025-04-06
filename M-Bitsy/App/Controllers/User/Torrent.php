<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TorrentsLogic;

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
        $torrent = TorrentsLogic::fetchIdDetails($id, $this->current_user, $action);

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
        if(!empty($_POST)) {
            TorrentsLogic::submitIdDetails($id, $this->current_user);
        } else {
            showMessage("torrent/$id", LANG['ACTION_FAILED']);
        }
    }

    // Delete Torrent Default Page
    public function deleteok()
    {
        if (!$this->current_user->delete_torrents == 'yes') {
            showMessage(URLROOT, LANG["NO__PERMISSION"]);
        }

        if (!empty($_POST)) {
            TorrentsLogic::delete($this->current_user);
        } else {
            showMessage(URLROOT, LANG["NO__PERMISSION"]);
        }
    }
}