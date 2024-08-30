<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\NewsModel;

/*
* News Controller
*/
class Adminnews extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(2);
        if ($this->current_user->class < CONFIG['_MODERATOR']) {
            showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
        }
    }

    // News Default Page
    public function index()
    {
    
        if (!empty($_POST)) {
            (new NewsModel)->insertOrUpdate();
        } else {
            // Get News Data
            $news = (new NewsModel)->fetchAll('*', '', 'ORDER BY added DESC');

            // Data For View
            $data = [
                'page_title' => LANG["NEWS"],
                'V_news' => $news
            ];
       
            // Load View
            $this->view('news', $data, 'admin');
        }
    }

    // Delete News Form Submit
    public function delete($id)
    {
    
        (new NewsModel)->delete($id, $this->current_user->id);
    }

}