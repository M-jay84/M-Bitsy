<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CatagoriesLogic;
use App\Models\TagsLogic;
use App\Models\TorrentLangLogic;
use App\Models\UploadLogic;

/*
* Upload Controller
*/
class Upload extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!$this->current_user->can_upload == 'yes') {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (CONFIG['UPLOADERSONLY'] && !$this->current_user->isStaff) {
            showMessage(URLROOT, LANG["UPLOAD_ONLY_FOR_UPLOADERS"]);
        }
    }

    // Upload Default Page
    public function index()
    {
        // Get Announce List
        $announce_urls = explode(",", URLROOT . CONFIG['ANNOUNCELIST']);

        // Data For View
        $data = [
            'TITLE' => LANG["UPLOAD"],
            'ANNOUNCES' => $announce_urls,
            'CATAGORYS' => CatagoriesLogic::genreList(),
            'TAGS' => TagsLogic::getAll(),
            'LANGUAGES' => TorrentLangLogic::langList(),
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->template('user-upload', $data, 'user');
    }

    // Upload Form Submit
    public function submit()
    {
        if (!empty($_POST)) {
            // Upload Torrent
            UploadLogic::torrent($this->current_user);
        } else {
            showMessage(URLROOT, LANG["ACTION_FAILED"]);
        }
    }

}