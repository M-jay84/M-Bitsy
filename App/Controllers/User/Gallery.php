<?php

namespace App\Controllers\User;

use App\Libs\User;
use App\Models\UsersModel;

/*
* Gallery Controller
*/
class Gallery 
{
    // No Default Page
    public function index()
    {
        Redirect(Url(), Txt("NOTHING_FOUND"));
    }

    // View Image Page
    public function images($file_hash)
    {
        $USER = new User(0, 1);
        // Open Image
        $view_image = UPLOADDIR . "/attachment/$file_hash.data";
        if (file_exists($view_image)) {
            echo '<img src="'. data_uri($view_image, $file_hash) .'">';
        }
    }

    // View Avatars Page
    public function avatar()
    {
        $USER = new User(0, 1);
        // Get Data
        $avatars = UsersModel::avatarPagination();

        // Init Data
        $data = [
            'V_title' => Txt("AVATARS"),
            'V_avatars' => $avatars['stmt'],
            'V_pager' => $avatars['pager'],
            'USER' => $USER
        ];

        // Load View
        template('gallery', $data, 'user');
    }

}