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
            $langdropdown .= "<option value=\"0\">" . LANG["LANGUAGE"] . "</option>\n";
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

    // Fetch All Torrent Languages
    public function fetchAll()
    {
        $sql = DB::newSelect('torrentlang', '*', '', 'ORDER BY sort_index ASC');

        return $sql;
    }

    // Admin Lang Add/Insert
    public function insertOrUpdate()
    {
        $name=Input('name');
        $sort_index=Input('sort_index');
        $image=Input('image');
        $id=Input('id');

        // Check Correct Input
        if ($name == "") {
            showMessage(Url('admintorrentlang'), LANG["MISSING_FORM_DATA"]);
        }
                
        if (!$id) {
            // Insert Torrent Lang
            $ins = DB::insert('torrentlang', ['name' => $name, 'sort_index' => $sort_index, 'image' => $image]);
        } else {
            // Update Torrent Lang
            DB::update('torrentlang', ['name','sort_index','image'], ['id='], [$name, $sort_index, $image, $id]);
            showMessage(Url('admintorrentlang'), LANG["UPDATE_COMPLETE"]);
        }

        if ($ins) {
            showMessage(Url('admintorrentlang'), LANG["SUCCESS"]);
        } else {
            showMessage(Url('admintorrentlang/add'), LANG["ACTION_FAILED"]);
        }
    }

    // Admin Torrent Lang Update
    public function update($id, $sure)
    {
        if ($sure == 'sure') {
            $newlangid = Input('newlangid', 'int');
            DB::update('torrents', ['torrentlang'], ['torrentlang='], [$newlangid, $id]);
        DB::delete('torrentlang', ['id' => $id]);
            showMessage(Url('admintorrentlang'), LANG["ACTION_COMPLETE"]);
        } else {
            showMessage(
                Url('admintorrentlang'), 
                "<form method='post' action='". CONFIG['URLROOT'] ."/admintorrentlang/delete/$id/sure'>
            <label for='newlangid'>".LANG["MOVE_LANG"]."</label>
            <input id='newlangid' type='text' class='form-control' name='newlangid'>
            <input type='submit' class='btn btn-sm ttbtn' value='". LANG["SUBMIT"]."' />
            </form>");
        }
    }
}