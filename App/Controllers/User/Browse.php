<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TorrentsModel;

/*
* Broswe Controller
*/
class Browse
{
    // Search Browse Default Page todo (tidy up)
    public function index()
    {
        $USER = new User(0, 1);
        
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        } else {
            $browse = TorrentsModel::countBrowse();

            // Init Data
            $data = [
                'V_title' => Txt("BROWSE_TORRENTS"),
                'V_torrents' => $browse['res'],
                'V_pager' => $browse['pagerbuttons'],
                'V_catsquery' => $browse['catsquery'],
                'V_url' => $browse['search']['url'],
                'V_parent_cat' => $browse['search']['parent_cat'],
                'V_count' => $browse['count'],
                'V_wherecatina' => $browse['search']['wherecatina'],
                'V_cats' => $browse['cats'],
                'USER' => $USER
            ];

            // Load View
            template('browse', $data, 'user');
        }
    }

}