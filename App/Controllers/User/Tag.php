<?php

namespace App\Controllers\User;

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
        $this->current_user = $this->currentuser(1);
    }

    // Search By Tag Default Page
    public function index($name)
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }
        // Get Tag Data
        $tags = (new TagsModel)->search($name);

        // Data For View
        $data = [
            'page_title' => LANG["SEARCH"],
            'V_tags' => $tags['stmt']
        ];

        // Load View
        $this->view('tag', $data, 'user');
    }

}