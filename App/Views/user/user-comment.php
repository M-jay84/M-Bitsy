<?php block_begin($TITLE) ?>
<?php if ($TYPE != 'user') : ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= LANG["ADD"] ?></a>
<p>
<?php endif; ?>
<?php if ($TYPE == "news") : ?>
    <div class="tt_blockcenter rounded p-3">
        <div class="text-center">
            <b><?= htmlspecialchars($ROW['title']) ?></b><br>
            <?= format_comment($ROW['body']) ?><br>
        </div>
    </div><br>
<?php endif;
if ($COUNT) {
    commenttable($COMMENT, $TYPE);
} else {
    print("<br><center><b>" . LANG["NOCOMMENTS"] . "</b></center><br>\n");
} ?>
<?php $PAGER ?>

<form id="submit" method="post" action="<?= Url("comment/submit/$TARGET_ID/$TYPE") ?>">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <?php echo textbbcode("submit", "text"); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= LANG["SAVE"] ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>