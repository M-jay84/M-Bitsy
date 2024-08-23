<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $page_title ?>
    </div>
    <div class="p-3">
        <form method=post name=message action='<?= Url('contact/submit') ?>'>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
            <p class='text-center'><?= LANG["LEAVE_EMAIL"] ?></p>
            <input id="subject" type="text" class="form-control fullwidth" name="subject" minlength="3" maxlength="200" placeholder="<?= LANG["SUBJECT"] ?>" required autofocus><br>
            <textarea class="form-control" id="msg" name="msg" rows="15"></textarea><br>
            <div class="text-center">
                <?php captcha_html() ?>
                <button type="submit" class="btn ttbtn"><?= LANG['SUBMIT'] ?></button>
            </div>
            <div class="margin-top20 text-center p-2">
                <a href="<?= Url('login') ?>"><?= LANG["LOGIN"] ?></a> |
                <a href="<?= Url('signup') ?>"><?= LANG["SIGNUP"] ?></a> |
                <a href="<?= Url('recover') ?>"><?= LANG["RECOVER"] ?></a> 
            </div>
        </form>
    </div>
</div>