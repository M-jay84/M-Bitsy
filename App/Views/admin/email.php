<?php block_begin($V_title) ?>

<div class="tt_blockcenter rounded p-2">
    <form name="Form" method="post" action="<?= Url('adminemail') ?>">
        <input type='text' class='form-control fullwidth' name='subject' placeholder="<?= Txt('SUBJECT') ?>"/><br>
        <?php foreach ($V_groups as $row) { ?>
            <input type="checkbox" name="groups[]" value="<?= $row["group_id"] ?>" /> <?= $row["level"] ?><br><?php
        } 
        echo textbbcode("Form", "body"); ?>
        <div class='text-center'>
            <input type="submit" class='btn btn-sm ttbtn' value="<?= Txt('SEND') ?>" />
        </div>
    </form>
</div>
<?php block_end() ?>