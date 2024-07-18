<?php block_begin($V_title) ?>
<?php usermenu($USER, $USER->has('id'), 'messages'); ?><br>
<div class="row  rounded">
    <p class="text-center">
        <?= messagemenu(); ?>
    </p>
    <p class="text-center">
        <b><?= $V_messages['subject'] ?></b>
    </p>
    <span class="text-end">
        <?= $V_button ?>
    </span>
    <div class='tt_block rounded p-3'>
        <div class='row'>
            <div class='col-12'>
                <?= coloredname($V_messages['sender']) ?>
                <small><?= Txt("POSTED_AT") ?> <?= $V_messages['added'] ?> </small>
            </div>
            <div class='col-md-12'>
                <?= format_comment($V_messages['msg']); ?>
            </div>
        </div>
    </div>
    <?php if ($V_messages['previous']) : ?>
    <p class="text-center"><?= Txt("HISTORY") ?></p>
        <?php foreach ($V_messages['previous'] as $msg) : ?>
       <div class='tt_block rounded p-3'>
        <div class='row'>
            <div class='col-12'>
                <?= coloredname($msg['sender']) ?>
                <small><?= Txt("POSTED_AT") ?> <?= $msg['added'] ?> </small>
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
                <input type="hidden" name="receiver" value="<?= $V_messages['replyto'] ?>" />
                <input type="hidden" name="Update" value="create" />
                <input type="hidden" name="subject" value="<?= $V_messages['subject'] ?>" />
                <input type="hidden" name="history" value="<?= $V_messages['history'] ?>" />
                <div class="text-center">
                    <?= Txt("TO") ?> <?= coloredname($V_messages['replyto']) ?><br>
                    <?= Txt("SUBJECT") ?> <?= $V_messages['subject'] ?><br>
                </div>
                <div class="form-floating mb-4">
                    <select name="template" class='form-select'>
                        <option name='0'>---</option>
                        <?php echo $V_messages['messages'] ?>
                    </select>
                    <label for="template"><?= Txt("TEMPLATE") ?>:</label>
                </div>
                <?php print textbbcode("form", "body"); ?>
                <p class="text-center">
                    <button type="submit" class="btn-sm ttbtn" name="Update" value="create"><?= Txt("CREATE") ?></button>&nbsp;
                    <label><?= Txt("SAVE_COPY") ?></label>
                    <input type="checkbox" name="save" checked='Checked'>&nbsp;
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class='btn ttbtn' data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                <button type="submit" class='btn ttbtn'><?= Txt("SUBMIT") ?></button>
            </div>
        </div>
    </div>
</div>
</form>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
        <div class="modal-content">
            <form name="form1" action="<?= Url("message/update/$messages[id]") ?>" method="post"><br>
                <div class="modal-body">
                    <?php print textbbcode("form1", "msg", $V_messages['msg']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class='btn ttbtn m-2' data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class='btn ttbtn m-2'><?= Txt("SUBMIT") ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php block_end() ?>