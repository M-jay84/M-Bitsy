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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Search Browse Default
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        } else {
            $browse =TorrentsModel::countBrowse();

            // Data For View
            $data = [
                'TITLE' => LANG["BROWSE_TORRENTS"],
                'TORRENTS' => $browse['res'],
                'PAGER' => $browse['pagerbuttons'],
                'CATSQUERY' => $browse['catsquery'],
                'URL' => $browse['search']['url'],
                'PARENT_CAT' => $browse['search']['parent_cat'],
                'COUNT' => $browse['count'],
                'WHERE_CAT_IN' => $browse['search']['wherecatina'],
                'CATS' => $browse['cats'],
                'LANG' => $browse['lang'],
                'KEYWORD' => $browse['search']['keyword'],
                'USER' => $this->current_user
            ];

            // Load View
            $this->view('user-browse', $data, 'user');
        }
    }

}