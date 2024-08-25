<?php

if ($_SESSION['id'] || !CONFIG['MEMBERSONLY']) {
    $keyword = $_GET['keyword'] ?? '';
    block_begin(LANG["SEARCH"]);
    ?>
	<form method="get" action="<?= Url('search') ?>" class="form-inline">
		<div class="input-group">
			<input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($keyword); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn ttbtn"><?= LANG["SEARCH"] ?></button>
			</span>
		</div>
	</form>
	<?php
    block_end();
}