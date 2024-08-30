<?php

namespace App\Controllers\User;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\UsersModel;

/*
* Gallery Controller
*/
class Gallery extends Controller
{
    public $current_user;

    // Get Current User
    public function __construct()
    {
        $this->current_user = (new Auth)->user(1);
    }

    // No Default Page
    public function index()
    {
        showMessage(Url(), LANG["NOTHING_FOUND"]);
    }

    // View Image Page
    public function images($file_hash)
    {
        // Open Image
        $view_image = CONFIG['UPLOADDIR'] . "/attachment/$file_hash.data";
        if (file_exists($view_image)) {
            echo '<img src="'. data_uri($view_image, $file_hash) .'">';
        }
    }

    // View Avatars Page
    public function avatar()
    {
        // Get Data
        $avatars = UsersModel::avatarPagination();

        // Data For View
        $data = [
            'page_title' => LANG["AVATARS"],
            'V_avatars' => $avatars['stmt'],
            'V_pager' => $avatars['pager']
        ];

        // Load View
        $this->view('gallery', $data, 'user');
    }

}