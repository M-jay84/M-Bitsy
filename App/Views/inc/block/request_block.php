<?php

use App\Models\RequestsModel;

if ($_SESSION['id']) {
    block_begin(LANG["REQUESTS"]);
    $expires = 600; // Cache time in seconds 10 mins
    if (($rows = $TTCache->Get("sql_cache/request_block", $expires)) === false) {
        $requests = RequestsModel::joincat();
        $result = array();
        foreach ($requests as $row) {
            $result[] = $row;
        }
        $TTCache->Set("sql_cache/request_block", $result, $expires);
    }

    if (isset($result)) {
        foreach ($result as $row) {
            $smallname = htmlspecialchars(CutName($row["request"], 12));
            $smallnamereq = htmlspecialchars(CutName($row["cat"], 4));
            echo "<table cellspacing='0' cellpadding='3' width='100%' border='0'><tr><td width='55%'><small><a style='text-decoration: none;' title='" . $row["parent_cat"] . " : " . $row["cat"] . "'>" . $row["parent_cat"] . " : $smallnamereq </a></small></td><td width='45%'><a style='text-decoration: none;' title='" . $row["request"] . "' href='".Url("request")."'>$smallname</a></td></tr></table> \n";
        }
    } else {
        print("<center>No requests</center> \n");
    }
    block_end();
}