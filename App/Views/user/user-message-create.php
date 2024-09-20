<?php block_begin($TITLE) ?>
<p class="text-center">
<a href="<?= URLROOT ?>/message/mailbox/inbox"><button type="button" class="<?= activelink('inbox', 'inbox') ?>"><?= LANG["INBOX"] ?></button></a>&nbsp;
<a href="<?= URLROOT ?>/message/mailbox/outbox"><button type="button" class="<?= activelink('outbox', 'outbox') ?>"><?= LANG["OUTBOX"] ?></button></a>&nbsp;
<a href="<?= URLROOT ?>/message/create"><button type="button" class="<?= activelink('messages/create') ?>"><?= LANG["COMPOSE"] ?></button></a>
</p>
<form name="form" action="<?= Url('message/submit') ?>" method="post">
    <div class="row tt_blockcenter rounded p-3">
        <?= LANG["RECEIVER"] ?>

        <?php if ($ID) : ?>
            <input type="hidden" name="receiver" value="<?= $ID; ?>" />
            <?= coloredname($ID); ?><br>
        <?php else : ?>
            <input type="text" id="search-box" name="receiver" class="form-control" autocomplete="off" placeholder="User Name" />
            <div id="suggesstion-box"></div><br>
        <?php endif; ?>
        <?= LANG["SUBJECT"] ?>
        <input type="text" name="subject" class="form-control fullwidth" placeholder="<?= LANG["SUBJECT"] ?>" id="subject"><br><br>
        <?php print textbbcode("form", "body"); ?><br>
        <div class="text-center">
            <button type="submit" class="btn btn-sm ttbtn" name="Update" value="create"><?= LANG["SUBMIT"] ?></button>&nbsp;
            <label><?= LANG["SAVE_COPY"] ?></label>
            <input type="checkbox" name="save" checked='Checked'>&nbsp;
        </div>
    </div>
</form>
<?php block_end() ?>