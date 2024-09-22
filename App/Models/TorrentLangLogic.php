<?php

namespace App\Models;

use App\Libs\DB;

/*
*  Torrent Langauage Table Data
*/
class TorrentLangLogic
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

}