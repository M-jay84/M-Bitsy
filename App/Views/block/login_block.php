<?php

use App\Models\PeersModel;

if ($USER->has('id')) {
    $avatar = htmlspecialchars($USER->has('avatar'));
    if (!$avatar) {
        $avatar = Url('assets/images/misc/default_avatar.webp');
    }
    $maxslotdownload = PeersModel::fetchColumn('distinct torrent as count',  ['userid=','seeder='], [$USER->has('id'),'yes']);
    $slots = number_format($USER->has('maxslots')) . "/" . number_format($maxslotdownload);
    $userratio = $USER->has('downloaded') > 0 ? number_format($USER->has('uploaded') / $USER->has('downloaded'), 1) : "---";

    block_begin("<b>". coloredname($USER->has('id'))."</b>");
    ?>
    <div class='row'>
        <div class="col">
            <div class="text-center">
                <img src="<?= $avatar ?>" alt="Avatar" class='rounded-circle scaleA'/>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-5"><?= Txt("DOWNLOAD"); ?></dt>
                <dd class="col-sm-7"><?= mksize($USER->has('downloaded')) ?></dd>
                <dt class="col-sm-5"><?= Txt("UPLOAD") ?></dt>
                <dd class="col-sm-7"><?= mksize($USER->has('uploaded')) ?></dd>
                <dt class="col-sm-5"><?= Txt("CLASS") ?></dt>
                <dd class="col-sm-7"><?= Txt($USER->has('level')) ?></dd>
                <dt class="col-sm-5"><?= Txt("PRIVACY") ?></dt>
                <dd class="col-sm-7"><?= Txt($USER->has('privacy')) ?></dd>
                <dt class="col-sm-5"><?= Txt("BONUS") ?></dt>
                <dd class="col-sm-7"><a href="<?= Url('bonus') ?>"><?= $USER->has('seedbonus') ?></a></dd>
                <dt class="col-sm-5"><?= Txt("RATIO") ?></dt>
                <dd class="col-sm-7"><?= get_ratio_color($userratio) ?></dd>
                <dt class="col-sm-5"><?= Txt("SLOTS") ?></dt>
                <dd class="col-sm-7"><?= $slots ?></dd>
            </dl>
        </div>
    </div>
	<div class="text-center">
        <a href='<?= Url("profile/". $USER->has('id') ."") ?>'><button class="btn ttbtn"><?= Txt("ACCOUNT"); ?></button></a> <?php
        if ($USER->has('control_panel') == "yes") { ?>
		    <a href="<?= Url('admincp') ?>" class="btn ttbtn"><?= Txt("STAFFCP"); ?></a> <?php
        } ?>
	</div> <?php
    block_end();
} elseif (!CONFIG['MEMBERSONLY']) {
    block_begin('Login'); ?>
    <div class="justify-content-md-center">
        <form method="post" action="<?= Url('login/submit') ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_Token(); ?>" />
        <font face="verdana" size="1"><b><?= Txt("USERNAME") ?>:</b></font>
        <input type="text" class="form-control" name="username" />
	    <font face="verdana" size="1"><b><?= Txt("PASSWORD") ?>:</b></font>
        <input type="password"  class="form-control"  name="password"  />
        <div class="text-center">
	        <?php captcha_html(); ?>
	        <button type='submit' class='btn btn-sm ttbtn' value='Login'><?= Txt("LOGIN") ?></button>
        </div>
        <p class="text-center">[<a href="<?= Url('signup') ?>"><?= Txt("SIGNUP") ?></a>] / [<a href="<?= Url('recover') ?>"><?= Txt("RECOVER_ACCOUNT") ?></a>]
        </form>
    </div>  <?php
    block_end();
}