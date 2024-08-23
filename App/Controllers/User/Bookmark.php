<?php

namespace App\Controllers\User;

use App\Libs\Controller;
use App\Models\BookmarksModel;

/*
* Bookmark Controller (Home Page)
*/
class Bookmark extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = $this->currentuser(2);
    }

    // Book Mark Default Page
    public function index($id)
    {
    

        if ($this->current_user->class < CONFIG['_MODERATOR'] or $this->current_user->id != $id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            $bookmarks = (new BookmarksModel)->getAll($id);

            // Data For View
            $data = [
                'page_title' => LANG['BOOKMARKS'],
                'V_count' => $bookmarks['count'],
                'V_pager' => $bookmarks['pager'],
                'V_bookmarks' => $bookmarks['query'],
            'current_user' => $this->current_user
            ];
    
            // Load View
            $this->view('bookmark', $data, 'user');
        } 
    }

    // Add/Delete Bookmark Submit
    public function action($id = 0, $del = 0)
    {
        if ($this->current_user->class < CONFIG['_MODERATOR'] or $id != $this->current_user->id) {
            showMessage(Url(), LANG["NO_PERMISSION"]);
        } else {
            $data = ['current_user' => $this->current_user];
            (new BookmarksModel)->action($id, $del, $data);
        } 
    }

}