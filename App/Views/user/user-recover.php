<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $page_title ?>
    </div>
    <form method="post" action="<?= Url('recover') ?>"><br>
        <input type="hidden" name="csrf_token" value="<?php csrf_token() ?>" />
        <input type="email" name="email" id="email" class="form-control fullwidth" placeholder="<?= LANG["EMAIL"] ?>"><br>
        <div class="text-center">
            <?= captcha_html() ?>
            <button type="submit" class="btn ttbtn"><?= LANG['SUBMIT'] ?></button>
        </div>
        <p class="text-center p-2">
            <a href="<?= Url('login') ?>"><?= LANG["LOGIN"] ?></a> |
            <a href="<?= Url('signup') ?>"><?= LANG["SIGNUP"] ?></a>
        </p>
    </form>
</div>