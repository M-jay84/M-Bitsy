<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\CatagoriesModel;

/*
* Torrents Today Controller
*/
class Today extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(1);
    }

    // Search Today Default Page
    public function index()
    {
        if ($this->current_user->view_torrents != "yes" && CONFIG['MEMBERSONLY']) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }
        $date_time = get_date_time(gmtime() - (3600 * 24)); // the 24 is the hours you want listed
        
        // Get Cat Data
        $cats = (new CatagoriesModel)->fetchAll('id, name', '', 'ORDER BY sort_index');

        // Data For View
        $data = [
            'page_title' => LANG["TODAYS_TORRENTS"],
            'V_date_time' => $date_time,
            'V_cats' => $cats
        ];

        // Load View
        $this->view('today', $data, 'user');
    }

}