<?php

namespace App\Models;

use App\Libs\DB;

/*
* Stylesheet Table Data
*/
class StylesheetsModel
{
    // Styles Dropdown
    public static function getStyleDropDown($stylesheet)
    {
        $stylesheets = '';
        $ss_r = DB::fetchAll('stylesheets', '*', '');
        $ss_sa = array();
        foreach ($ss_r as $ss_a) {
            $ss_id = $ss_a["uri"];
            $ss_name = $ss_a["name"];
            $ss_sa[$ss_name] = $ss_id;
        }
        ksort($ss_sa);
        reset($ss_sa);
        //while (list($ss_name, $ss_id) = thisEach($ss_sa)) {
        foreach($ss_sa as $ss_name => $ss_id) {
            if ($ss_id == $stylesheet) {
                $ss = " selected='selected'";
            } else {
                $ss = "";
            }
            $stylesheets .= "<option value='$ss_id'$ss>$ss_name</option>\n";
        }
        return $stylesheets;
    }
	
    // Get All Stylesheets
    public static function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('stylesheets', $columns, $where, $values, $other);
        return  $stmt;
    }

    // Admin Add Style
    public static function insert()
    {
        
        // Check Correct Input
        if (empty($_POST['name']) || empty($_POST['uri'])) {
            Redirect(Url('adminstylesheet/add'), Txt("MISSING_FORM_DATA"));
        }

        $qry = DB::insert('stylesheets', ['name' => Input('name'), 'uri' => Input('uri')]);
        if ($qry) {
            Redirect(Url('adminstylesheet/add'), "Theme '" . htmlspecialchars(Input('name')) . "' added.");
        } elseif ($qry->errorCode() == 1062) {
            Redirect(Url('adminstylesheet/add'), Txt("ALREADY_THERE"));
        } else {
            Redirect(Url('adminstylesheet/add'), Txt("ACTION_FAILED") . " " . $qry->errorInfo());
        }
    }

    // Admin Delete Style
    public static function delete()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('stylesheets', 'id', $ids);
        DB::run("UPDATE `users` SET `stylesheet` = " . CONFIG['DEFAULTTHEME'] . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
        Redirect(Url('adminstylesheet'), Txt("DEL_COMPLETE"));
    }

}