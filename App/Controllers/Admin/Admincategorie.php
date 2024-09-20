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
            showMessage(URLROOT, LANG["NO_PERMISSION"]);
        }
    }

    // Categorie Page
    public function index()
    {
        if (!empty($_POST)) {
            // Add/Edit Catagories
            CatagoriesModel::insertOrUpdate();
        } else {
            // Show All Catagories
            $cats = CatagoriesModel::fetchAll('*', '', 'ORDER BY parent_cat ASC, sort_index ASC');
        
            // Data For View
            $data = [
                'TITLE' => LANG["CATEGORIES"],
                'CATAGORIES' => $cats
            ];
        
            // Load View
            $this->view('admin-categorie', $data, 'admin');
        }
    }

    // Categorie Delete
    public function delete($id, $sure = 0)
    {
        // Delete Catagorie
        CatagoriesModel::deleteAndUpdateTorrent($id, $sure);
    }

}