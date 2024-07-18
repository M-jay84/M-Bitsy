<?php
use App\Libs\DB;
use App\Models\TorrentsModel;

if ($USER->has('id') || !CONFIG['MEMBERSONLY']) {
    block_begin(Txt("LATEST_TORRENTS"));
    $expire = 900; // time in seconds
    if (($latestuploadsrecords = $TTCache->Get("block/latestuploadsblock", $expire)) === false) {
        $latestuploadsquery = TorrentsModel::fetchAll('id, name, size, seeders, leechers', ['banned=','visible='], ['no','yes'], ' ORDER BY id DESC LIMIT 5');
        $latestuploadsrecords = array();
        foreach ($latestuploadsquery as $latestuploadsrecord) {
            $latestuploadsrecords[] = $latestuploadsrecord;
        }
        $TTCache->Set("block/latestuploadsblock", $latestuploadsrecords, $expire);
    }

    if ($latestuploadsrecords) {
        foreach ($latestuploadsrecords as $row) {
            $smallname = htmlspecialchars(CutName($row['name'], 20)) . "..."; ?>
			<a href='<?= Url("torrent/$row[id]") ?>' title='<?= htmlspecialchars($row["name"]); ?>'><?= $smallname; ?></a><br>
			<span><?= Txt("SIZE") ?>: <?= mksize($row["size"]); ?></span><br>
		<?php
        }
    } else { ?>
		<p calss="text-center"><?= Txt("NOTHING_FOUND") ?></p>
	    <?php
    } ?>
    <?php
    block_end();
}