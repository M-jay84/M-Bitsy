<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\NewsLogic;

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
            NewsLogic::insertOrUpdate();
        } else {
            // Show All News
            $news_query = "SELECT * FROM `news` ORDER BY added DESC";
            $news = DB::run($news_query);

            // Data For View
            $data = [
                'TITLE' => LANG["NEWS"],
                'NEWS' => $news
            ];
       
            // Load View
            $this->template('admin-news', $data, 'admin');
        }
    }

    // Delete News
    public function delete($id)
    {
        NewsLogic::delete($id, $this->current_user->id);
    }

}