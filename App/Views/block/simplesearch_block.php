<?php

if ($USER->has('id') || !CONFIG['MEMBERSONLY']) {
    $keyword = $_GET['keyword'] ?? '';
    block_begin(Txt("SEARCH"));
    ?>
	<form method="get" action="<?= Url('search') ?>" class="form-inline">
		<div class="input-group">
			<input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($keyword); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn ttbtn"><?= Txt("SEARCH") ?></button>
			</span>
		</div>
	</form>
	<?php
    block_end();
}