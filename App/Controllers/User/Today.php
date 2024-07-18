<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\CatagoriesModel;

/*
* Torrents Today Controller
*/
class Today 
{
    // Search Today Default Page
    public function index()
    {
        // Verify User/Guest
        $USER = new User(0, 1);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }
        $date_time = get_date_time(gmtime() - (3600 * 24)); // the 24 is the hours you want listed
        
        // Get Cat Data
        $cats = CatagoriesModel::fetchAll('id, name', '', 'ORDER BY sort_index');

        // Init Data
        $data = [
            'V_title' => Txt("TODAYS_TORRENTS"),
            'V_date_time' => $date_time,
            'V_cats' => $cats,
            'USER' => $USER
        ];

        // Load View
        template('today', $data, 'user');
    }

}