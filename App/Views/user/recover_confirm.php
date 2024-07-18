<div class="tt_blockcenter rounded">
	<div class="tt_blockhead text-center">
		<?= $V_title ?>
	</div>
	<form method="post" action="<?= Url("recover/confirm/$V_id/$V_secret") ?>">
		<input type="hidden" name="csrf_token" value="<?php csrf_token() ?>" />
		<input id="password" type="text" class="form-control fullwidth" name="password" placeholder="<?= Txt("NEW_PASSWORD") ?>" minlength="3" maxlength="35" required autofocus><br>
		<input id="password1" type="text" class="form-control fullwidth" name="password1" placeholder="<?= Txt("REPEAT") ?>" minlength="3" maxlength="35" required autofocus><br>
		<div class="text-center">
			<button type="submit" class="btn ttbtn"><?= Txt("SUBMIT") ?></button>
		</div>
	</form>
</div>