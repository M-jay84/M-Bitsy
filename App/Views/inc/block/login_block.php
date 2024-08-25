<?php

use App\Libs\DB;

if ($_SESSION['id']) {
    $avatar = htmlspecialchars($_SESSION['avatar']);
    if (!$avatar) {
        $avatar = Url('assets/images/misc/default_avatar.webp');
    }
    $maxslotdownload = DB::fetchColumn('peers', 'distinct torrent as count',  ['userid=','seeder='], [$_SESSION['id'],'yes']);
    $slots = number_format($_SESSION['maxslots']) . "/" . number_format($maxslotdownload);
    $userratio = $_SESSION['downloaded'] > 0 ? number_format($_SESSION['uploaded'] / $_SESSION['downloaded'], 1) : "---";

    block_begin("<b>". coloredname($_SESSION['id'])."</b>");
    ?>
    <div class='row'>
        <div class="col">
            <div class="text-center">
                <img src="<?= $avatar ?>" alt="Avatar" class='rounded-circle scaleA'/>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-5"><?= LANG["DOWNLOAD"]; ?></dt>
                <dd class="col-sm-7"><?= mksize($_SESSION['downloaded']) ?></dd>
                <dt class="col-sm-5"><?= LANG["UPLOAD"] ?></dt>
                <dd class="col-sm-7"><?= mksize($_SESSION['uploaded']) ?></dd>
                <dt class="col-sm-5"><?= LANG["CLASS"] ?></dt>
                <dd class="col-sm-7"><?= $_SESSION['level'] ?></dd>
                <dt class="col-sm-5"><?= LANG["PRIVACY"] ?></dt>
                <dd class="col-sm-7"><?= LANG[$_SESSION['privacy']] ?></dd>
                <dt class="col-sm-5"><?= LANG["BONUS"] ?></dt>
                <dd class="col-sm-7"><a href="<?= Url('bonus') ?>"><?= $_SESSION['seedbonus'] ?></a></dd>
                <dt class="col-sm-5"><?= LANG["RATIO"] ?></dt>
                <dd class="col-sm-7"><?= get_ratio_color($userratio) ?></dd>
                <dt class="col-sm-5"><?= LANG["SLOTS"] ?></dt>
                <dd class="col-sm-7"><?= $slots ?></dd>
            </dl>
        </div>
    </div>
	<div class="text-center">
        <a href='<?= Url("profile/". $_SESSION['id'] ."") ?>'><button class="btn ttbtn"><?= LANG["ACCOUNT"]; ?></button></a> <?php
        if ($_SESSION['control_panel'] == "yes") { ?>
		    <a href="<?= Url('admincp') ?>" class="btn ttbtn"><?= LANG["STAFFCP"]; ?></a> <?php
        } ?>
	</div> <?php
    block_end();
} elseif (!CONFIG['MEMBERSONLY']) {
    block_begin('Login'); ?>
    <div class="justify-content-md-center">
        <form method="post" action="<?= Url('login/submit') ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_Token(); ?>" />
        <font face="verdana" size="1"><b><?= LANG['USERNAME'] ?>:</b></font>
        <input type="text" class="form-control" name="username" />
	    <font face="verdana" size="1"><b><?= LANG["PASSWORD"] ?>:</b></font>
        <input type="password"  class="form-control"  name="password"  />
        <div class="text-center">
	        <?php captcha_html(); ?>
	        <button type='submit' class='btn btn-sm ttbtn' value='Login'><?= LANG["LOGIN"] ?></button>
        </div>
        <p class="text-center">[<a href="<?= Url('signup') ?>"><?= LANG["SIGNUP"] ?></a>] / [<a href="<?= Url('recover') ?>"><?= LANG["RECOVER"] ?></a>]
        </form>
    </div>  <?php
    block_end();
}