<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TorrentsModel;

/*
* Broswe Controller
*/
class Browse extends Controller
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

    // Search Browse Default Page todo (tidy up)
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            $this->showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        } else {
            $browse =TorrentsModel::countBrowse();

            // Data For View
            $data = [
                'page_title' => LANG["BROWSE_TORRENTS"],
                'V_torrents' => $browse['res'],
                'V_pager' => $browse['pagerbuttons'],
                'V_catsquery' => $browse['catsquery'],
                'V_url' => $browse['search']['url'],
                'V_parent_cat' => $browse['search']['parent_cat'],
                'V_count' => $browse['count'],
                'V_wherecatina' => $browse['search']['wherecatina'],
                'V_cats' => $browse['cats'],
                'current_user' => $this->current_user
            ];

            // Load View
            $this->view('user-browse', $data, 'user');
        }
    }

}