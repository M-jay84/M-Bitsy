<?php block_begin($page_title) ?>
<div class='tt_blockcenter rounded p-3'>
	<form method=post action='<?= Url('adminuseradd') ?>'>
     	<?= LANG["NAME"] ?>
		<input id="name" type="text" class="form-control fullwidth" name="username" minlength="3" maxlength="25" required autofocus><br>
		<?= LANG["PASSWORD"] ?>
		<input id="name" type="password" class="form-control fullwidth" name="password" minlength="3" maxlength="25" required autofocus><br>
		<?= LANG["CONFIRM"] ?>
		<input id="name" type="password" class="form-control fullwidth" name="password2" minlength="3" maxlength="25" required autofocus><br>
		<?= LANG["EMAIL"] ?>
		<input id="name" type="text" class="form-control fullwidth" name="email" minlength="3" required autofocus><br>
		<div class='text-center'><button type="submit" class="btn ttbtn  btn-sm"><?= LANG['SUBMIT'] ?></button></div>
	</form>
</div>
<?php block_end() ?>