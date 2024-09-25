<?php
use App\Libs\DB;

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
    block_begin(LANG["LATEST_TORRENTS"]);
    $expire = 900; // time in seconds
    if (($latestuploadsrecords = $TTCache->Get("sql.latestuploadsblock", $expire)) === false) {
        $torrents_query = "SELECT id, name, size, seeders, leechers FROM `torrents` 
                          WHERE banned=? AND visible =? ORDER BY id DESC LIMIT 5";
        $latestuploadsquery =DB::run($torrents_query, ['banned=','visible='], ['no','yes']);
        $latestuploadsrecords = array();
        foreach ($latestuploadsquery as $latestuploadsrecord) {
            $latestuploadsrecords[] = $latestuploadsrecord;
        }
        $TTCache->Set("sql.latestuploadsblock", $latestuploadsrecords, $expire);
    }

    if ($latestuploadsrecords) {
        foreach ($latestuploadsrecords as $row) {
            $smallname = htmlspecialchars(CutName($row['name'], 20)) . "..."; ?>
			<a href='<?= Url("torrent/$row[id]") ?>' title='<?= htmlspecialchars($row["name"]); ?>'><?= $smallname; ?></a><br>
			<span><?= LANG["SIZE"] ?>: <?= mksize($row["size"]); ?></span><br>
		<?php
        }
    } else { ?>
		<p calss="text-center"><?= LANG["NOTHING_FOUND"] ?></p>
	    <?php
    } ?>
    <?php
    block_end();
}