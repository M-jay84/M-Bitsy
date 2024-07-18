<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\SnatchedModel;

/*
* Snatch Controller
*/
class Snatch 
{
    // Snatched Torrent Default Page // todo more test
    public function index($id, $type)
    {
        $USER = new User(0, 2);
        if ($!USER->permission('view_torrents') && $id != $USER->has('id')) {
            Redirect(Url(), Txt("NO_TORRENT_VIEW"));
        }
        
        if ($type == 'complete') {
            $snatched = SnatchedModel::join('snatched.tid = ? AND snatched.completed != 0', $id);
            $cols = 'torrent';
        } elseif ($type == 'torrent') {
            $snatched = SnatchedModel::join('snatched.tid = ?', $id);
            $cols = 'torrent';
        } elseif ($type == 'user') {
            $snatched = SnatchedModel::join('snatched.uid = ?', $id);
            $cols = 'user';
        }

        // Init Data
        $data = [
            'V_title' => Txt("SNATCH_RESULT"),
            'V_cols' => $cols,
            'V_snatched' => $snatched['stmt'],
            'V_count' => $snatched['count'],
            'V_pager' => $snatched['pager'],
            'USER' => $USER
        ];

        // Load View
        template('snatch', $data, 'user');
    }

    // Trade Snatched Default Page
    public function trade()
    {
        $USER = new User(0, 2);
        if (!empty($_POST)) {
                SnatchedModel::submitTrade();
        } else {
            // Get Snatched Data
            $snatched = SnatchedModel::trade($USER->has('id'));

            // Init Data
            $data = [
                'V_title' => Txt('TRADE'),
                'V_snatched' => $snatched,
                'USER' => $USER
            ];

            // Load View
            template('snatch_trade', $data, 'user');
        }
    }

}