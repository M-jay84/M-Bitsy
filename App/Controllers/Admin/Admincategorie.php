<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\CatagoriesModel;

/*
* Catagorys Controller
*/
class Admincategorie extends Controller
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

    // Categorie Default Page
    public function index()
    {
    
        if (!empty($_POST)) {
            CatagoriesModel::insertOrUpdate();
        } else {
            // Get Categorie
            $cats = CatagoriesModel::fetchAll('*', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
            // Data For View
            $data = [
                'page_title' => LANG["TORRENT_CATEGORIES"],
                'V_cats' => $cats
            ];
        
            // Load View
            $this->view('admin-categorie', $data, 'admin');
        }
    }

    // Categorie Delete
    public function delete($id, $sure = false)
    {
        CatagoriesModel::deleteAndUpdateTorrent($id, $sure);
    }

}