<?php

use App\Models\UsersModel;

if ($USER->has('id')) {
    $expires = 120; // Cache time in seconds 2 mins
    block_begin(Txt("ONLINE_USERS"));
    if (($rows = $TTCache->Get("block/usersonline_block", $expires)) === false) {
        $res = UsersModel::fetchAll('id, username',
         ['enabled=','status=','privacy !=', "UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <="],
        ['yes','confirmed','strong', get_date_time(), 900]);
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("block/usersonline_block", $rows, $expires);
    }

    if (!$rows) { ?>
	   <p class="text-center"><?= Txt("NO_USERS_ONLINE"); ?></p>
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