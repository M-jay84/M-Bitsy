<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\TorrentsLogic;

/*
* Catagories Table Data
*/
class CatagoriesLogic
{

    // Admin Delete Catagorie
    public static function deleteAndUpdateTorrent($id, $sure = 0)
    {
        if ($sure) {
            // Move Any Torrents to Other Cat & Delete Cat
            $newcatid = Validate::Input('newcat', 'int');
            $torrents_query = "UPDATE torrents SET category = ? WHERE category=?";
            DB::run($torrents_query, [$newcatid, $id]); //move torrents to a new cat
            $categories_query = "DELETE FROM `categories` WHERE id = ?";
            DB::run($categories_query, [$id]);
            showMessage('admincategorie', LANG["DEL_COMPLETE"]);
        } else {
            // Select Cat 2 Move Kept Torrent Too
            showMessage('admincategorie', 
                "<form method='post' action='" .  URLROOT . "/admincategorie/delete/$id/sure'>                                 
                <div class='tt_blockcenter rounded'>
                <b>Category ID to move all Torrent To: </b>
                <input type='text' name='newcat' /> (Cat ID)<br><br>
                <p class='text-center'>
                    <input type='submit' class='btn btn-sm ttbtn' value='".LANG["SUBMIT"]."' />
                </div>
                </p>", 30);
        }

    }

    // Admin Delete Catagorie
    public static function getAll()
    {
        $categories_query = "SELECT * FROM `categories` ORDER BY parent_cat ASC, sort_index ASC";
        $cats = DB::run($categories_query);
        return $cats;
    }

    // Admin Add/Edit Catagorie
    public static function insertOrUpdate()
    {
        $parent_cat = Validate::Input('parent_cat');
        $name = Validate::Input('name');
        $sort_index = Validate::Input('sort_index');
        $image = Validate::Input('image');
        $id = Validate::Input('id');

        // Check Correct Input
        if ($parent_cat == "" || $name == "") {
            showMessage('admincategorie/add', LANG["MISSING_FORM_DATA"]);
        }
        
        if (!$id) {
            // Insert Categorie

            $categories_query = "INSERT INTO `categories` (name,parent_cat,sort_index,image) VALUES (?,?,?,?)";
            $ins =  DB::run($categories_query,
            [$name, $parent_cat, $sort_index, $image]);

            if ($ins) {
                showMessage('admincategorie', LANG["SUCCESS"]);
            } else {
                showMessage('admincategorie/add', LANG["ACTION_FAILED"]);
            }
        } else {
            // Update
            $categories_query = "UPDATE categories SET parent_cat = ? AND name = ? AND sort_index = ?  AND image = ? WHERE id=?";
            DB::run($categories_query, [$parent_cat, $name, $sort_index, $image, $id]);
            showMessage('admincategorie', LANG["SUCCESS"]);
        }
    }

    // Get Parent
    public static function getCatByParent()
    {
        $categories_query = "SELECT distinct parent_cat FROM categories ORDER BY parent_cat";
        $row = DB::run($categories_query);
        return $row;
    }

    // List Genres
    public static function genreList()
    {
        $ret = array();
        $categories_query = "SELECT id, name, parent_cat FROM `categories` ORDER BY parent_cat ASC, sort_index ASC";
        $res = DB::run($categories_query);
        foreach ($res as $row) {
            $ret[] = $row;
        }
        return $ret;
    }

    // Catagories Dropdown
    public static function dropdown($category = 0)
    {
        $data = self::genreList();
        $catdropdown = '';
        if (!$category) {
            $catdropdown .= "<option value='0'>Catagorie</option>\n";
        }
        foreach ($data as $row) {
            $catdropdown .= "<option value=\"" . $row["id"] . "\"";
            if ($row["id"] == $category) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
        }
    
        return $catdropdown;
    }

}