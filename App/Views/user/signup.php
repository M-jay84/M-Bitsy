<div class="tt_blockcenter rounded">
    <div class="tt_blockhead text-center">
        <?= $V_title ?>
    </div>
    <div class="p-3">
        <form method="post" class="form-horizontal" action="<?= Url('signup') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
            <?php if ($V_invite != 0) : ?>
                <input type="hidden" name="invite" value="<?= $_GET["invite"] ?>" />
                <input type="hidden" name="secret" value="<?= htmlspecialchars($_GET["secret"]) ?>" />
            <?php endif; ?>
            <p class="text-center"><?= Txt("COOKIES"); ?></p>
            <input id="wantusername" type="text" class="form-control fullwidth" name="wantusername" minlength="3" maxlength="25" placeholder="<?= Txt("USERNAME") ?>" required><br>
            <input id="wantpassword" type="password" class="form-control fullwidth" name="wantpassword" minlength="6" maxlength="25" placeholder="<?= Txt("PASSWORD") ?>" requireds><br>
            <input id="passagain" type="password" class="form-control fullwidth" name="passagain" minlength="6" maxlength="25" placeholder="<?= Txt("CONFIRM") ?>" required autofocus><br>
            <?php if ($V_invite == 0) : ?>
                <input id="email" type="email" class="form-control fullwidth" name="email" minlength="3" maxlength="100" placeholder="<?= Txt("EMAIL") ?>" required autofocus><br>
            <?php endif; ?>
            <input id=" age" type="text" class="form-control fullwidth" name="age" minlength="2" placeholder="<?= Txt("AGE") ?>" required autofocus><br>
            <select name="country" class='form-control fullwidth'>
                <?php echo $V_country ?>
            </select><br>
            <select name="gender" class='form-control fullwidth'>
                <option value="0"><?= Txt("GENDER") ?></option>
                <option name="gender" id="Male" value="Male"><?= Txt("MALE") ?></option>
                <option name="gender" id="Female" value="Female"><?= Txt("FEMALE") ?></option>
            </select><br>
            <input id="client" type="text" class="form-control fullwidth" name="client" placeholder="<?= Txt("PREF_BITTORRENT_CLIENT") ?>" minlength="3" maxlength="25" required autofocus><br>
            <div class="text-center">
                <?= captcha_html() ?>
                <button type="submit" class="btn ttbtn"><?= Txt("SIGNUP"); ?></button>
            </div>
            <div class="p-2 text-center">
                <a href="<?= Url('login') ?>"><?= Txt("LOGIN") ?></a> |
                <a href="<?= Url('recover') ?>"><?= Txt("RECOVER_ACCOUNT") ?></a>
            </div>
        </form>
    </div>
</div>