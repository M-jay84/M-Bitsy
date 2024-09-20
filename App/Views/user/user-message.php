<?php block_begin($TITLE) ?>
<div class="row  rounded">
    <p class="text-center">
        <a href="<?= URLROOT ?>/message/mailbox/inbox"><button type="button" class="<?= activelink('inbox', 'inbox') ?>"><?= LANG["INBOX"] ?></button></a>&nbsp;
        <a href="<?= URLROOT ?>/message/mailbox/outbox"><button type="button" class="<?= activelink('outbox', 'outbox') ?>"><?= LANG["OUTBOX"] ?></button></a>&nbsp;
        <a href="<?= URLROOT ?>/message/create"><button type="button" class="<?= activelink('messages/create') ?>"><?= LANG["COMPOSE"] ?></button></a>

    </p>
    <p class="text-center">
        <b><?= $MESSAGES['subject'] ?></b>
    </p>
    <span class="text-end">
        <a href='javascript:;' class='addAttr' data-bs-toggle='modal' data-bs-target='#replyModal' '><button class='btn ttbtn m-2'><i class='fa fa-home me-2'></i>Reply</button></a>
    </span>
    <div class='tt_block rounded p-3'>
        <div class='row'>
            <div class='col-12'>
                <?= coloredname($MESSAGES['sender']) ?>
                <small><?= LANG["POSTED_AT"] ?> <?= $MESSAGES['added'] ?> </small>
            </div>
            <div class='col-md-12'>
                <?= format_comment($MESSAGES['msg']); ?>
            </div>
        </div>
    </div>
    <?php if ($MESSAGES['previous']) : ?>
    <p class="text-center"><?= LANG["HISTORY"] ?></p>
        <?php foreach ($MESSAGES['previous'] as $msg) : ?>
       <div class='tt_block rounded p-3'>
        <div class='row'>
            <div class='col-12'>
                <?= coloredname($msg['sender']) ?>
                <small><?= LANG["POSTED_AT"] ?> <?= $msg['added'] ?> </small>
            </div>
            <div class='col-md-12 p-2'>
                <?= format_comment($msg['msg']); ?>
            </div>
        </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Reply Modal -->
<form name="form" action="<?= Url('message/submit') ?>" method="post">
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
        <div class="modal-content">
            <div class="modal-body">
                <input type="hidden" name="receiver" value="<?= $MESSAGES['replyto'] ?>" />
                <input type="hidden" name="Update" value="create" />
                <input type="hidden" name="subject" value="<?= $MESSAGES['subject'] ?>" />
                <input type="hidden" name="history" value="<?= $MESSAGES['history'] ?>" />
                <div class="text-center">
                    <?= LANG["REPLY"] ?> <h4><b><?= coloredname($MESSAGES['replyto']) ?></b></h4><br>
                    <?= LANG["SUBJECT"] ?> <?= $MESSAGES['subject'] ?><br>
                </div>
                <?php print textbbcode("form", "body"); ?>
                <p class="text-center">
                    <button type="submit" class="btn-sm ttbtn" name="Update" value="create"><?= LANG["CREATE"] ?></button>&nbsp;
                    <label><?= LANG["SAVE_COPY"] ?></label>
                    <input type="checkbox" name="save" checked='Checked'>&nbsp;
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class='btn ttbtn' data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                <button type="submit" class='btn ttbtn'><?= LANG['SUBMIT'] ?></button>
            </div>
        </div>
    </div>
</div>
</form>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
        <div class="modal-content">
            <form name="form1" action="<?= Url("message/update/$MESSAGES[id]") ?>" method="post"><br>
                <div class="modal-body">
                    <?php print textbbcode("form1", "msg", $MESSAGES['msg']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class='btn ttbtn m-2'><?= LANG['SUBMIT'] ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php block_end() ?>