<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $V_title ?>
    </div>
    <form method="post" action="<?= Url('recover') ?>"><br>
        <input type="hidden" name="csrf_token" value="<?php csrf_token() ?>" />
        <input type="email" name="email" id="email" class="form-control fullwidth" placeholder="<?= Txt("EMAIL") ?>"><br>
        <div class="text-center">
            <?= captcha_html() ?>
            <button type="submit" class="btn ttbtn"><?= Txt("SUBMIT") ?></button>
        </div>
        <p class="text-center p-2">
            <a href="<?= Url('login') ?>"><?= Txt("LOGIN") ?></a> |
            <a href="<?= Url('signup') ?>"><?= Txt("SIGNUP") ?></a>
        </p>
    </form>
</div>