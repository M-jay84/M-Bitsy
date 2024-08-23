<?php

namespace App\Models;

use App\Libs\DB;

/*
* Stylesheet Table Data
*/
class StylesheetsModel
{
    // Styles Dropdown
    public function getStyleDropDown($stylesheet)
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
    public function fetchAll($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::newSelect('stylesheets', $columns, $where, $values, $other);
        return  $stmt;
    }

    // Admin Add Style
    public function insert()
    {
        
        // Check Correct Input
        if (empty($_POST['name']) || empty($_POST['uri'])) {
            showMessage(Url('adminstylesheet/add'), LANG["MISSING_FORM_DATA"]);
        }

        $qry = DB::insert('stylesheets', ['name' => Input('name'), 'uri' => Input('uri')]);
        if ($qry) {
            showMessage(Url('adminstylesheet/add'), "Theme '" . htmlspecialchars(Input('name')) . "' added.");
        } elseif ($qry->errorCode() == 1062) {
            showMessage(Url('adminstylesheet/add'), LANG["ALREADY_THERE"]);
        } else {
            showMessage(Url('adminstylesheet/add'), LANG["ACTION_FAILED"] . " " . $qry->errorInfo());
        }
    }

    // Admin Delete Style
    public function delete()
    {
        $ids = multiIds('ids');
        DB::deleteByIds('stylesheets', 'id', $ids);
        DB::run("UPDATE `users` SET `stylesheet` = " . CONFIG['DEFAULTTHEME'] . " WHERE stylesheet NOT IN (SELECT id FROM stylesheets)");
        showMessage(Url('adminstylesheet'), LANG["DEL_COMPLETE"]);
    }

}