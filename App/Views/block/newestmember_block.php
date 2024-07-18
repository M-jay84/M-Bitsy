<?php

use App\Models\UsersModel;

if ($USER->has('id')) {
	block_begin(Txt("NEWEST_MEMBERS"));
    $expire = 600; // time in seconds
    if (($rows = $TTCache->Get("block/newestmember_block", $expire)) === false) {
        $res = UsersModel::fetchAll('id, username', ['enabled=','status=','privacy !='], ['yes','confirmed','strong'], "ORDER BY id DESC LIMIT 5");
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("block/newestmember_block", $rows, $expire);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?= Txt("NOTHING_FOUND") ?></p>
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