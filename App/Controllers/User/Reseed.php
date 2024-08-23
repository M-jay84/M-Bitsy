<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Reseed Controller
*/
class Reseed extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(1);
    }

    // Request Reseed Form Submit todo (tidy up)
    public function index($id)
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }
        (new TorrentsModel)->reseed($id, $this->current_user);
    }

}