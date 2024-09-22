<?php

namespace App\Models;

use App\Libs\DB;

/*
* Countries Table Data
*/
class CountriesLogic
{
    // Country Flag //todo view
    public static function fetchRow($country, $pic = 0)
    { 
        $carr = DB::fetchRow('countries', 'name,flagpic', ['id='], [$country], 'LIMIT 1');
        if ($carr) {
            $country = $pic == 1 ? "<img src='" . URLROOT . "/assets/images/languages/$carr[flagpic]' title='" . htmlspecialchars($carr['name']) . "' alt='" . htmlspecialchars($carr['name']) . "' />" : htmlspecialchars($carr['name']);
        } else {
            $country = $pic == 1 ? "<img src='" . URLROOT . "/assets/images/languages/unknown.webp' alt='Unknown' />" : "<b>Unknown</b>";
        }
        return $country;
    }

    //Country Dropdown
    public static function dropdown($country = 0)
    {
        $data = array();
        $stmt = DB::fetchAll('countries', 'id, name', '', 'ORDER BY name');
        foreach ($stmt as $row) {
            $data[] = $row;
        }
        $countries = '';
        if (!$country) {
            $countries .= "<option value='12'>United Kindom</option>\n";
        }
        foreach ($data as $row) {
            $countries .= "<option value=\"" . $row["id"] . "\"";
            if ($row["id"] == $country) {
                $countries .= " selected=\"selected\"";
            }
            $countries .= ">" . htmlspecialchars($row["name"]) . "</option>\n";
        }
    
        return $countries;
    }
}