<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $page_title ?>
    </div>
    <?php if (CONFIG['MEMBERSONLY']) : ?>
       <p class='text-center'><b><?= LANG["MEMBERS_ONLY"] ?></b></p>
    <?php endif; ?>
    <div class="p-3">
        <form method="post" action="<?= Url('login') ?>" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
            <div class="form p-4">
                <input id="username" type="text" class="form-control fullwidth" name="username" minlength="3" maxlength="25" required autofocus placeholder="<?= LANG['USERNAME'] ?>">
            </div>
            <div class="form p-4">
                <input id="password" type="password" class="form-control fullwidth" name="password" minlength="6" maxlength="16" required data-eye placeholder="<?= LANG['PASSWORD'] ?>">
            </div>
            <div class="text-center">
                <?= captcha_html(); ?>
                <button type="submit" class="btn ttbtn"><?= LANG["LOGIN"] ?></button><br><br>
                <p class='text-center'><i><?= LANG["COOKIES"] ?></i></p>
            </div>
            <div class="p-2 text-center">
                <a href="<?= Url('signup') ?>"><?= LANG["SIGNUP"] ?></a> |
                <a href="<?= Url('recover') ?>"><?= LANG["RECOVER"] ?></a>
            </div><br>
        </form>
    </div>
</div>