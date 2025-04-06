<?php

use App\Libs\DB;

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
	block_begin(LANG["BROWSE_TORRENTS"]);
	$categories_query = "SELECT distinct parent_cat, id, image FROM `categories` ORDER BY parent_cat";
    $catsquery = DB::run($categories_query);
	?>
	<div class="list-group">
		<a href="<?= Url('browse') ?>" class="list-group-item"><i class="fa <?= $catsrow["image"]; ?> tticon"></i> <?= LANG["SHOW_ALL"] ?></a>
	    <?php foreach ($catsquery as $catsrow) { ?>
			<a href="<?= Url("browse?cat=$catsrow[id]") ?>" class="list-group-item"><i class="fa <?= $catsrow["image"]; ?> tticon"></i> <?= $catsrow["parent_cat"]; ?></a>
	    <?php } ?>
	</div><?php
	block_end();
}