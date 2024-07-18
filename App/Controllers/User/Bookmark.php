<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\BookmarksModel;

/*
* Bookmark Controller (Home Page)
*/
class Bookmark 
{
    // Book Mark Default Page
    public function index($id)
    {
        $USER = new User(0, 2);

        if (!$USER->isStaff($id)) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            $bookmarks = BookmarksModel::getAll($id);

            // Init Data
            $data = [
                'V_title' => Txt('BOOKMARKS'),
                'V_count' => $bookmarks['count'],
                'V_pager' => $bookmarks['pager'],
                'V_bookmarks' => $bookmarks['query'],
                'USER' => $USER
            ];
    
            // Load View
            template('bookmark', $data, 'user');
        } 
    }

    // Add/Delete Bookmark Submit
    public function action($id = 0, $del = 0)
    {
        $USER = new User(0, 2, true);
        if (!$USER->isStaff($USER->has('id'))) {
            Redirect(Url(), Txt("NO_PERMISSION"));
        } else {
            BookmarksModel::action($id, $del);
        } 
    }

}