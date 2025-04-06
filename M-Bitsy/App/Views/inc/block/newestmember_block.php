<?php

use App\Libs\DB;

if ($_SESSION['id']) {
	block_begin(LANG["NEWEST_MEMBERS"]);
    $expire = 600; // time in seconds
    if (($rows = $TTCache->Get("sql.newestmember_block", $expire)) === false) {
        $users_query = "SELECT id,username FROM `users` WHERE enabled=? AND status=? AND privacy !=? ORDER BY id DESC LIMIT 5";
        $res = DB::run($users_query,  ['yes','confirmed','strong']);
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("sql.newestmember_block", $rows, $expire);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?= LANG["NOTHING_FOUND"] ?></p>
       <?php
    } else { ?>
		<div>
	    <?php
        foreach ($rows as $row) {?>
			<a href='<?= Url("profile/$row[id]") ?>'><?= coloredname($row["id"]); ?></a>
	        <?php
        } ?>
		</div>
    <?php
    } 
    block_end();
}