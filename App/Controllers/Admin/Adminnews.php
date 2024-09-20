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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // News
    public function index()
    {
        if (!empty($_POST)) {
            // Add/Edit News/Announcement
            NewsModel::insertOrUpdate();
        } else {
            // Show All News
            $news = NewsModel::fetchAll('*', '', 'ORDER BY added DESC');

            // Data For View
            $data = [
                'TITLE' => LANG["NEWS"],
                'NEWS' => $news
            ];
       
            // Load View
            $this->view('admin-news', $data, 'admin');
        }
    }

    // Delete News
    public function delete($id)
    {
        NewsModel::delete($id, $this->current_user->id);
    }

}