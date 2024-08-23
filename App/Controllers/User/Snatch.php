<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\SnatchedModel;

/*
* Snatch Controller
*/
class Snatch extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Snatched Torrent Default Page // todo more test
    public function index($id, $type)
    {
    
        if ($this->current_user->view_torrents && $id != $this->current_user->id) {
            showMessage(Url(), LANG["NO_TORRENT_VIEW"]);
        }
        
        if ($type == 'complete') {
            $snatched = (new SnatchedModel)->join('snatched.tid = ? AND snatched.completed != 0', $id);
            $cols = 'torrent';
        } elseif ($type == 'torrent') {
            $snatched = (new SnatchedModel)->join('snatched.tid = ?', $id);
            $cols = 'torrent';
        } elseif ($type == 'user') {
            $snatched = (new SnatchedModel)->join('snatched.uid = ?', $id);
            $cols = 'user';
        }

        // Data For View
        $data = [
            'page_title' => LANG["SNATCH_RESULT"],
            'V_cols' => $cols,
            'V_snatched' => $snatched['stmt'],
            'V_count' => $snatched['count'],
            'V_pager' => $snatched['pager']
        ];

        // Load View
        $this->view('snatch', $data, 'user');
    }

    // Trade Snatched Default Page
    public function trade()
    {
    
        if (!empty($_POST)) {
            (new SnatchedModel)->submitTrade($this->current_user);
        } else {
            // Get Snatched Data
            $snatched = (new SnatchedModel)->trade($this->current_user->id);

            // Data For View
            $data = [
                'page_title' => LANG['TRADE'],
                'V_snatched' => $snatched
            ];

            // Load View
            $this->view('snatch_trade', $data, 'user');
        }
    }

}