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
        $this->current_user = new Auth();

        if (!$this->current_user->isStaff) {
            $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // News Default Page
    public function index()
    {
        if (!empty($_POST)) {
            NewsModel::insertOrUpdate();
        } else {
            // Get News Data
            $news = NewsModel::fetchAll('*', '', 'ORDER BY added DESC');

            // Data For View
            $data = [
                'page_title' => LANG["NEWS"],
                'V_news' => $news
            ];
       
            // Load View
            $this->view('admin-news', $data, 'admin');
        }
    }

    // Delete News Form Submit
    public function delete($id)
    {
        NewsModel::delete($id, $this->current_user->id);
    }

}