<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TagsModel;

/*
* Tags Controller
*/
class Tag extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Search By Tag Default Page
    public function index($name)
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }
        // Get Tag Data
        $tags = TagsModel::search($name);

        // Data For View
        $data = [
            'page_title' => LANG["SEARCH"],
            'V_tags' => $tags['stmt']
        ];

        // Load View
        $this->view('user-tag', $data, 'user');
    }

}