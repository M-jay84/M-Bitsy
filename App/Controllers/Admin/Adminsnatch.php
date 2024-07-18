<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\SnatchedModel;

/*
* Snatched Controller
*/
class Adminsnatch 
{
    // Hit & Runs Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!CONFIG['HNR_ON']) {
            Redirect(Url(), Txt("HNRS_BEEN_DISABLED"));
        }

        // Pagination
        $snatched = SnatchedModel::join('snatched.hnr = ?', 'yes');

        // Init Data
        $data = [
            'V_title' => Txt("HNR"),
            'V_count' => $snatched['count'],
            'V_pager' => $snatched['pager'],
            'V_snatched' => $snatched['stmt'],
            'USER' => $USER
        ];

        // Load View
        template('snatch', $data, 'admin');
    }

    // Hit & Runs Default Page
    public function submit()
    {
        $USER = new User(_MODERATOR, 2);
        SnatchedModel::deleteByIds();
    }
}