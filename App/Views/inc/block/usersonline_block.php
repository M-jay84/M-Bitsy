<?php

use App\Libs\DB;

if ($_SESSION['id']) {
    $expires = 120; // Cache time in seconds 2 mins
    block_begin(LANG["ONLINE_USERS"]);
    if (($rows = $TTCache->Get("sql_cache/usersonline_block", $expires)) === false) {
        $res = DB::fetchAll('users', 'id, username',
         ['enabled=','status=','privacy !=', "UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <="],
        ['yes','confirmed','strong', CONFIG['TT_DATE'], 900]);
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("sql_cache/usersonline_block", $rows, $expires);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?= LANG["NO_USERS_ONLINE"]; ?></p>
       <?php
    } else {
        for ($i = 0, $cnt = count($rows), $n = $cnt - 1; $i < $cnt; $i++) {
        $row = &$rows[$i];?>
        <b><?= coloredname($row["id"]); ?></b><?php echo ($i < $n ? ", " : ""); ?>
	    <?php
        }
    }
    block_end();
}