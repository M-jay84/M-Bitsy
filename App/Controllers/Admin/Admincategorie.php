<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Libs\DB;
use App\Models\CatagoriesLogic;

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
            CatagoriesLogic::insertOrUpdate();
        } else {
            // Show All Catagories
            $categories_query = "SELECT * FROM `categories` ORDER BY parent_cat ASC, sort_index ASC";
            $cats = DB::run($categories_query);
        
            // Data For View
            $data = [
                'TITLE' => LANG["CATEGORIES"],
                'CATAGORIES' => $cats
            ];
        
            // Load View
            $this->template('admin-categorie', $data, 'admin');
        }
    }

    // Categorie Delete
    public function delete($id, $sure = 0)
    {
        // Delete Catagorie
        CatagoriesLogic::deleteAndUpdateTorrent($id, $sure);
    }

}