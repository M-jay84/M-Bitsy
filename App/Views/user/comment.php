<?php block_begin($V_title) ?>
<?php if ($V_type != 'user') : ?>
<p class="text-center">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= Txt("ADD") ?></a>
<p>
<?php endif; ?>
<?php if ($V_type == "news") : ?>
    <div class="tt_blockcenter rounded p-3">
        <div class="text-center">
            <b><?= htmlspecialchars($V_row['title']) ?></b><br>
            <?= format_comment($V_row['body']) ?><br>
        </div>
    </div><br>
<?php endif;
if ($V_count) {
    commenttable($USER, $V_comments, $V_type);
} else {
    print("<br><center><b>" . Txt("NOCOMMENTS") . "</b></center><br>\n");
} ?>
<?php $V_pager ?>

<form id="submit" method="post" action="<?= Url("comment/submit/$V_targetid/$V_type") ?>">
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body">
                    <?php echo textbbcode("submit", "text"); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                    <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SAVE") ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php block_end() ?>