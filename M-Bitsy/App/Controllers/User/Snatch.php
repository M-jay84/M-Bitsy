<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\SnatchedLogic;

/*
* Snatch Controller
*/
class Snatch extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = new Auth();

        if (!$this->current_user->loggedin) {
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Snatched Torrents
    public function index($id, $type)
    {
        if ($this->current_user->view_torrents && $id != $this->current_user->id) {
            showMessage(URLROOT, LANG["NO_TORRENT_VIEW"]);
        }
        
        if ($type == 'complete') {
            $snatched = SnatchedLogic::join('snatched.tid = ? AND snatched.completed != 0', $id);
            $cols = 'torrent';
        } elseif ($type == 'torrent') {
            $snatched = SnatchedLogic::join('snatched.tid = ?', $id);
            $cols = 'torrent';
        } elseif ($type == 'user') {
            $snatched = SnatchedLogic::join('snatched.uid = ?', $id);
            $cols = 'user';
        }

        // Data For View
        $data = [
            'TITLE' => LANG["SNATCH_RESULT"],
            'COLS' => $cols,
            'SNATCHED' => $snatched['stmt'],
            'COUNT' => $snatched['count'],
            'PAGER' => $snatched['pager']
        ];

        // Load View
        $this->template('user-snatch', $data, 'user');
    }

    // Trade Snatched Default Page
    public function trade()
    {
        if (!empty($_POST)) {
            SnatchedLogic::submitTrade($this->current_user);
        } else {
            // Data For View
            $data = [
                'TITLE' => LANG['TRADE'],
                'SNATCHED' => SnatchedLogic::trade($this->current_user->id)
            ];

            // Load View
            $this->template('user-snatch-trade', $data, 'user');
        }
    }

}