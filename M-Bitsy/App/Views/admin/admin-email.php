<?php block_begin($TITLE) ?>

<div class="tt_blockcenter rounded p-2">
    <form name="Form" method="post" action="<?= Url('adminemail/sendemail') ?>">
        <input type='text' class='form-control fullwidth' name='subject' placeholder="<?= LANG['SUBJECT'] ?>"/><br>
        <?php foreach ($GROUPS as $row) { ?>
            <input type="checkbox" name="groups[]" value="<?= $row["group_id"] ?>" /> <?= $row["level"] ?><br><?php
        } 
        echo textbbcode("Form", "body"); ?>
        <div class='text-center'>
            <input type="submit" class='btn btn-sm ttbtn' value="<?= LANG['SUBMIT'] ?>" />
        </div>
    </form>
</div>
<?php block_end() ?>