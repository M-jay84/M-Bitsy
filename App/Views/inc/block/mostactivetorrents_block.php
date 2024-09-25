<?php

use App\Libs\DB;

if ($_SESSION['id']) {
    block_begin(LANG["MOST_ACTIVE"]);
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("sql.mostactivetorrents_block", $expires)) === false) {
        $torrents_query = "SELECT id, name, seeders, leechers FROM `torrents` 
                 WHERE banned=? AND visible =? ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT 10 ";
        $res = DB::run($torrents_query, ['no', 'yes']);

        $categories_query = "SELECT distinct parent_cat FROM `categories` ORDER BY parent_cat";
        $catsquery = DB::run($categories_query);
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("sql.mostactivetorrents_block", $rows, $expires);
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