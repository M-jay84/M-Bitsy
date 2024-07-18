<?php
use App\Models\CatagoriesModel;
if ($USER->has('id')) {
    block_begin(Txt("SEARCH"));
	$search = $_GET['search'] ?? '';
    ?>
	<form method="get" action="<?= Url('search') ?>">
		<input type="text" name="search" style="width: 95%" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="<?= Txt("SEARCH") ?>"/><br />
		    <select name="cat"  style="width: 95%" class='form-select'>
			<option value="0">(<?= Txt("ALL") ?>)</option>
			    <?php echo CatagoriesModel::dropdown($_GET["cat"]); ?>
		    </select><br />
		    <select name="incldead" style="width: 95%"  class='form-select'>
			<option value="0"><?= Txt("ACTIVE") ?></option>
			<option value="1"><?= Txt("INCLUDE_DEAD") ?></option>
			<option value="2"><?= Txt("ONLY_DEAD") ?></option>
		    </select><br />
	    <?php if (CONFIG['ALLOWEXTERNAL']) : ?>
		    <select name="inclexternal" style="width: 95%" class='form-select' >
			<option value="0"><?= Txt("LOCAL") ?>/<?= Txt("EXTERNAL") ?></option>
			<option value="1"><?= Txt("LOCAL_ONLY") ?></option>
			<option value="2"><?= Txt("EXTERNAL_ONLY") ?></option>
		    </select><br />
	    <?php endif; ?>
		<div class="text-center">
			<button type="submit" class="btn ttbtn center-block"><?= Txt("SEARCH") ?></button>
		</div>
	</form>
	<?php
	block_end();
}