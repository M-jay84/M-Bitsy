<?php block_begin($V_title) ?>
<p class="text-center">
    <?= Txt('SEND_EXCEPTION') ?>
</p>

<form method="post">
    <div class="form-group">
        <textarea class="form-control" name="newcontents" style="height:600px;">
            <?= $V_filecontents ?>
        </textarea><br>
    </div>
    <p class="text-center">
        <input type="submit" class="btn ttbtn btn-sm" value="<?= Txt("SUBMIT") ?>">
    </p>
</form>
<?php block_end() ?>