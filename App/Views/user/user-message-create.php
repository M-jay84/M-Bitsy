<?php block_begin($page_title) ?>
<?php usermenu($current_user->id, 'messages');
echo messagemenu(); ?><br>
<form name="form" action="<?= Url('message/submit') ?>" method="post">
    <div class="row tt_blockcenter rounded p-3">
        <?= LANG["RECEIVER"] ?>

        <?php if ($V_id) : ?>
            <input type="hidden" name="receiver" value="<?= $V_id; ?>" />
            <?= coloredname($V_id); ?><br>
        <?php else : ?>
            <input type="text" id="search-box" name="receiver" class="form-control" autocomplete="off" placeholder="User Name" />
            <div id="suggesstion-box"></div><br>
        <?php endif; ?>

        <?= LANG["TEMPLATE"] ?>
        <select name="template" class='form-select'>
            <?= $V_templates ?>
        </select><br>
        <?= LANG["SUBJECT"] ?>
        <input type="text" name="subject" class="form-control fullwidth" placeholder="<?= LANG["SUBJECT"] ?>" id="subject"><br><br>
        <?php print textbbcode("form", "body"); ?><br>
        <div class="text-center">
            <button type="submit" class="btn btn-sm ttbtn" name="Update" value="create"><?= LANG["SUBMIT"] ?></button>&nbsp;
            <label><?= LANG["SAVE_COPY"] ?></label>
            <input type="checkbox" name="save" checked='Checked'>&nbsp;
            <button type="submit" class="btn btn-sm ttbtn" name="Update" value="draft"><?= LANG["DRAFT"] ?></button>
            <button type="submit" class="btn btn-sm ttbtn" name="Update" value="template"><?= LANG["TEMPLATE"] ?></button>
        </div>
    </div>
</form>
<?php block_end() ?>