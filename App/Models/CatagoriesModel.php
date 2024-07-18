<?php

namespace App\Models;

use App\Libs\DB;
use App\Models\TorrentsModel;

/*
* Catagories Table Data
*/
class CatagoriesModel
{
    // Fetch All Catagories
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('categories', $columns, $where, $values, $other);
		return $stmt;
	}
		
    // Fetch Catagorie Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('categories', $columns, $where, $values, $other);
		return $stmt;
	}

    // Admin Delete Catagorie
    public static function deleteAndUpdateTorrent($id, $sure = 0)
    {
        // Form Submit OR View
        if ($sure == 'sure') {
            // Delete Categorie
            $newcatid = Input('newcat', 'int');
            TorrentsModel::update(['category'], ['category='], [$newcatid, $id]); //move torrents to a new cat
            DB::delete('categories', ['id' => $id]);
            Redirect(Url('admincategorie'), Txt("DEL_COMPLETE"));
        } else {
                    Redirect(Url('admincategorie'), 
                        "<form method='post' action='".URLROOT."/admincategorie/delete/$id/sure'>                                 <div class='tt_blockcenter rounded'>
                            <b>Category ID to move all Torrent To: </b>
                            <input type='text' name='newcat' /> (Cat ID)<br><br>
                            <div class='text-center'>
                                <input type='submit' class='btn btn-sm ttbtn' value='".Txt("SUBMIT")."' />
                            </div>
                        </form>");
        }

    }

    // Admin Add/Edit Catagorie
    public static function insertOrUpdate()
    {
        $parent_cat = Input('parent_cat');
        $name = Input('name');
        $sort_index = Input('sort_index');
        $image = Input('image');
        $id = Input('id');

        // Check Correct Input
        if ($parent_cat == "" || $name == "") {
            Redirect(Url('admincategorie/add'), Txt("MISSING_FORM_DATA"));
        }
        
        if (!$id) {
            // Insert Categorie
            $ins = DB::insert('categories', ['name' => $name, 'parent_cat' => $parent_cat, 'sort_index' => $sort_index, 'image' => $image]);
            if ($ins) {
                Redirect(Url('admincategorie'), Txt("SUCCESS"));
            } else {
                Redirect(Url('admincategorie/add'), Txt("ACTION_FAILED"));
            }
        } else {
            // Update
            DB::update('categories', ['parent_cat','name','sort_index','image'], ['id='], [$parent_cat, $name, $sort_index, $image, $id]);
            Redirect(Url('admincategorie'), Txt("SUCCESS"));
        }
    }

    // Get Parent
    public static function getCatByParent()
    {
        $row = DB::run("SELECT distinct parent_cat  FROM categories ORDER BY parent_cat");
        return $row;
    }

    // List Genres
    public static function genreList()
    {
        $ret = array();
        $res = DB::fetchAll('categories', 'id, name, parent_cat', '', 'ORDER BY parent_cat ASC, sort_index ASC');
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