<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TagsLogic;

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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Search By Tag
    public function index($name)
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }
        // Get Tag Data
        $tags = TagsLogic::search($name);

        // Data For View
        $data = [
            'TITLE' => LANG["SEARCH"],
            'TAGS' => $tags['stmt']
        ];

        // Load View
        $this->template('user-tag', $data, 'user');
    }

}