<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CatagoriesModel;
use App\Models\TagsModel;
use App\Models\TorrentLangModel;
use App\Models\UploadModel;

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
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (!$this->current_user->can_upload == 'yes') {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
        if (CONFIG['UPLOADERSONLY'] && !$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["UPLOAD_ONLY_FOR_UPLOADERS"]);
        }
    }

    // Upload Default Page
    public function index()
    {
        // Get Announce List
        $announce_urls = explode(",", URLROOT . CONFIG['ANNOUNCELIST']);

        // Data For View
        $data = [
            'page_title' => LANG["UPLOAD"],
            'announce_urls' => $announce_urls,
            'catagories' => CatagoriesModel::genreList(),
            'tags' => TagsModel::getAll(),
            'languages' => TorrentLangModel::langList(),
            'current_user' => $this->current_user
        ];
        
        // Load View
        $this->view('user-upload', $data, 'user');
    }

    // Upload Form Submit // TODO
    public function submit()
    {
        if (!empty($_POST)) {
            $data = UploadModel::torrent($this->current_user);
            $this->showMessage("torrent/$data[id]", $data['message']);
        } else {
            $this->showMessage(URLROOT, LANG["ACTION_FAILED"]);
        }
    }

}