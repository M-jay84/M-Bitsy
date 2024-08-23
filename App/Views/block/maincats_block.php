<?php

use App\Libs\DB;

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
	block_begin(LANG["BROWSE_TORRENTS"]);
    $catsquery = DB::fetchAll('categories', 'distinct parent_cat, id, image', '', 'ORDER BY parent_cat');
	?>
	<div class="list-group">
		<a href="<?= Url('browse') ?>" class="list-group-item"><i class="fa <?= $catsrow["image"]; ?> tticon"></i> <?= LANG["SHOW_ALL"] ?></a>
	    <?php foreach ($catsquery as $catsrow) { ?>
			<a href="<?= Url("browse?cat=$catsrow[id]") ?>" class="list-group-item"><i class="fa <?= $catsrow["image"]; ?> tticon"></i> <?= $catsrow["parent_cat"]; ?></a>
	    <?php } ?>
	</div><?php
	block_end();
}