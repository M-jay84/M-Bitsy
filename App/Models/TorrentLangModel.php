<?php

namespace App\Models;

use App\Libs\DB;

/*
*  Torrent Langauage Table Data
*/
class TorrentLangModel
{
    // Lang Dropdown
    public static function langList($torrentlang = 0)
    {
        $langdropdown = "<select name=\"language\" class='form-select'>\n";
        $lang = array();
        $stmt = DB::fetchAll('torrentlang', 'id, name, image', '', 'ORDER BY sort_index, id');
        foreach ($stmt as $row) {
            $lang[] = $row;
        }
        if (!$torrentlang) {
            $langdropdown .= "<option value=\"0\">" . Txt("LANGUAGE") . "</option>\n";
        }
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $torrentlang) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        $langdropdown .= "</select>\n";
        return $langdropdown;
    }

    // Fetch All Languages
    public static function fetchAllLang($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('languages', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Fetch All Torrent Languages
    public static function fetchAll()
    {
        $sql = DB::newSelect('torrentlang', '*', '', 'ORDER BY sort_index ASC');

        return $sql;
    }

    // Admin Lang Add/Insert
    public static function insertOrUpdate()
    {
        $name=Input('name');
        $sort_index=Input('sort_index');
        $image=Input('image');
        $id=Input('id');

        // Check Correct Input
        if ($name == "") {
            Redirect(Url('admintorrentlang'), Txt("MISSING_FORM_DATA"));
        }
                
        if (!$id) {
            // Insert Torrent Lang
            $ins = DB::insert('torrentlang', ['name' => $name, 'sort_index' => $sort_index, 'image' => $image]);
        } else {
            // Update Torrent Lang
            DB::update('torrentlang', ['name','sort_index','image'], ['id='], [$name, $sort_index, $image, $id]);
            Redirect(Url('admintorrentlang'), Txt("UPDATE_COMPLETE"));
        }

        if ($ins) {
            Redirect(Url('admintorrentlang'), Txt("SUCCESS"));
        } else {
            Redirect(Url('admintorrentlang/add'), Txt("ACTION_FAILED"));
        }
    }

    // Admin Torrent Lang Update
    public static function update($id, $sure)
    {
        if ($sure == 'sure') {
            $newlangid = Input('newlangid', 'int');
            DB::update('torrents', ['torrentlang'], ['torrentlang='], [$newlangid, $id]);
        DB::delete('torrentlang', ['id' => $id]);
            Redirect(Url('admintorrentlang'), Txt("ACTION_COMPLETE"));
        } else {
            Redirect(
                Url('admintorrentlang'), 
                "<form method='post' action='". URLROOT ."/admintorrentlang/delete/$id/sure'>
            <label for='newlangid'>".Txt("MOVE_LANG")."</label>
            <input id='newlangid' type='text' class='form-control' name='newlangid'>
            <input type='submit' class='btn btn-sm ttbtn' value='". Txt("SUBMIT")."' />
            </form>");
        }
    }
}