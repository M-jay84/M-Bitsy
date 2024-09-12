<?php block_begin($page_title) ?>
<form method="post" action="<?= Url('invite/submit') ?>">
	<div class="tt_blockcenter rounded p-3">
	    <div class="form-floating mb-4">
			<input id="email" type="text" class="form-control fullwidth" name="email" minlength="7" required autofocus>
			<label for="email"><?= LANG["EMAIL"] ?>:</label>
		</div>
		<div class="form-floating mb-4">
			<textarea name="mess" class="form-control" id="mess" rows="7"></textarea>
			<label for="mess"><?= LANG["MESSAGE"] ?>:</label>
		</div>
		<div class="text-center">
			<button type="submit" class="btn ttbtn btn-sm"><?= LANG["SEND_AN_INVITE"] ?></button>
		</div>
	</div>
</form>
<?php block_end() ?>