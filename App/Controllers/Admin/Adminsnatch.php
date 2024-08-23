<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\SnatchedModel;

/*
* Snatched Controller
*/
class Adminsnatch extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // Hit & Runs Default Page
    public function index()
    {
    
        if (!CONFIG['HNR_ON']) {
            showMessage(Url(), LANG["HNRS_BEEN_DISABLED"]);
        }

        // Pagination
        $snatched = (new SnatchedModel)->join('snatched.hnr = ?', 'yes');

        // Data For View
        $data = [
            'page_title' => LANG["HNR"],
            'V_count' => $snatched['count'],
            'V_pager' => $snatched['pager'],
            'V_snatched' => $snatched['stmt']
        ];

        // Load View
        $this->view('snatch', $data, 'admin');
    }

    // Hit & Runs Default Page
    public function submit()
    {
    
        (new SnatchedModel)->deleteByIds();
    }
}