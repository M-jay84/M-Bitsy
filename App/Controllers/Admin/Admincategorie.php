<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\CatagoriesModel;

/*
* Catagorys Controller
*/
class Admincategorie 
{
    // Categorie Default Page
    public function index()
    {
        $USER = new User(_MODERATOR, 2);
        if (!empty($_POST)) {
            CatagoriesModel::insertOrUpdate();
        } else {
            // Get Categorie
            $cats = CatagoriesModel::fetchAll('*', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
            // Init Data
            $data = [
                'V_title' => Txt("TORRENT_CATEGORIES"),
                'V_cats' => $cats,
                'USER' => $USER
            ];
        
            // Load View
            template('categorie', $data, 'admin');
        }
    }

    // Categorie Delete
    public function delete($id, $sure = false)
    {
        $USER = new User(_MODERATOR, 2);
        CatagoriesModel::deleteAndUpdateTorrent($id, $sure);
    }

}