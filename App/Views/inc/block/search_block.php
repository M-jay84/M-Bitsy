<?php
use App\Models\CatagoriesModel;
if ($_SESSION['id']) {
    block_begin(LANG["SEARCH"]);
	$search = $_GET['search'] ?? '';
    ?>
	<form method="get" action="<?= Url('search') ?>">
		<input type="text" name="search" style="width: 95%" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="<?= LANG["SEARCH"] ?>"/><br />
		    <select name="cat"  style="width: 95%" class='form-select'>
			<option value="0">(<?= LANG["ALL"] ?>)</option>
			    <?php echo CatagoriesModel::dropdown($_GET["cat"]); ?>
		    </select><br />
		    <select name="incldead" style="width: 95%"  class='form-select'>
			<option value="0"><?= LANG["ACTIVE"] ?></option>
			<option value="1"><?= LANG["INCLUDE_DEAD"] ?></option>
			<option value="2"><?= LANG["ONLY_DEAD"] ?></option>
		    </select><br />
	    <?php if (CONFIG['ALLOWEXTERNAL']) : ?>
		    <select name="inclexternal" style="width: 95%" class='form-select' >
			<option value="0"><?= LANG["LOCAL"] ?>/<?= LANG["EXTERNAL"] ?></option>
			<option value="1">LOCAL_ONLY</option>
			<option value="2">EXTERNAL_ONLY</option>
		    </select><br />
	    <?php endif; ?>
		<div class="text-center">
			<button type="submit" class="btn ttbtn center-block"><?= LANG["SEARCH"] ?></button>
		</div>
	</form>
	<?php
	block_end();
}