<?php

use App\Libs\DB;

if ($_SESSION['id']) {
    block_begin(LANG["MOST_ACTIVE"]);
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("block/mostactivetorrents_block", $expires)) === false) {
        $res = DB::fetchAll('torrents', 'id, name, seeders, leechers', ['banned=', 'visible='], ['no', 'yes'], 'ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10');
        $catsquery = DB::fetchAll('categories', 'distinct parent_cat', '', 'ORDER BY parent_cat');
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("block/mostactivetorrents_block", $rows, $expires);
    }
    if ($rows) {
        foreach ($rows as $row) {
            $smallname = htmlspecialchars(CutName($row["name"], 20)) . "..."; ?>
            <a href='<?= Url("torrent/$row[id]") ?>' title='<?= htmlspecialchars($row["name"]); ?>'><?= $smallname; ?></a><br>
                <span class="label label-success"> S: <?= number_format($row['seeders']); ?></span>
                <span class="label label-warning"> L: <?= number_format($row['leechers']); ?></span>
            <br><?php
            }
    } else {
        ?>
	    <p><?= LANG["NOTHING_FOUND"] ?></p>
        <?php
    }
    block_end();
}