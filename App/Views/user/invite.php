<?php block_begin($V_title) ?>
<form method="post" action="<?= Url('invite/submit') ?>">
	<div class="tt_blockcenter rounded p-3">
	    <div class="form-floating mb-4">
			<input id="email" type="text" class="form-control fullwidth" name="email" minlength="7" required autofocus>
			<label for="email"><?= Txt("EMAIL") ?>:</label>
		</div>
		<div class="form-floating mb-4">
			<textarea name="mess" class="form-control" id="mess" rows="7"></textarea>
			<label for="mess"><?= Txt("MESSAGE") ?>:</label>
		</div>
		<div class="text-center">
			<button type="submit" class="btn ttbtn btn-sm"><?= Txt("SEND_AN_INVITE") ?></button>
		</div>
	</div>
</form>
<?php block_end() ?>