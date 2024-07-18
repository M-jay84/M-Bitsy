<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\TagsModel;

/*
* Tags Controller
*/
class Tag 
{
    // Search By Tag Default Page
    public function index($name)
    {
        $USER = new User(0, 1);
        if (!$USER->permission('view_torrents')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }
        // Get Tag Data
        $tags = TagsModel::search($name);

        // Init Data
        $data = [
            'V_title' => Txt("SEARCH"),
            'V_tags' => $tags['stmt'],
            'USER' => $USER
        ];

        // Load View
        template('tag', $data, 'user');
    }

}