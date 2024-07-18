<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\NewsModel;

/*
* News Controller
*/
class Adminnews 
{
    // News Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            NewsModel::insertOrUpdate();
        } else {
            // Get News Data
            $news = NewsModel::fetchAll('*', '', 'ORDER BY added DESC');

            // Init Data
            $data = [
                'V_title' => Txt("NEWS"),
                'V_news' => $news,
                'USER' => $USER
            ];
       
            // Load View
            template('news', $data, 'admin');
        }
    }

    // Delete News Form Submit
    public function delete($id)
    {
        $USER = new User(_MODERATOR, 2);
        NewsModel::delete($id);
    }

}