<?php
use App\Libs\DB;

if ($_SESSION['id']) {
    block_begin(LANG["SEEDERS_WANTED"]);
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("sql_cache/seedwanted_block", $expires)) === false) {
        $res = DB::fetchAll('torrents', 'id, name, seeders, leechers', ['seeders=','leechers=','banned=','external='], [0,0,'no','no'], 'ORDER BY leechers DESC LIMIT 5');
        $rows = array();
        foreach ($res as $row) {
            $rows[] = $row;
        }
        $TTCache->Set("sql_cache/seedwanted_block", $rows, $expires);
    }

    if (!$rows) { ?>
		<p class="text-center"><?= LANG["NOTHING_FOUND"]; ?></p>
	    <?php
    } else {
        foreach ($rows as $row) {
            $smallname = htmlspecialchars(CutName($row["name"], 20));?>
			<div class="pull-left"><a href="<?= Url("torrent/$row[id]") ?>" title="<?= htmlspecialchars($row["name"]); ?>"><?= $smallname; ?></a></div>
			<div class="pull-right"><span class="label label-waring"><?= LANG["LEECHERS"] ?>: <?= number_format($row["leechers"]); ?></span></div>
		    <?php
        }
    }
    block_end();
}