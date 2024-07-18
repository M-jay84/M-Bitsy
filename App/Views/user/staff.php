<?php block_begin($V_title) ?>
<?php foreach ($V_staff as $row) { ?>
    <?php if (!isset($V_table[$row["group_id"]])) { ?>
        <?php continue; ?>
    <?php }  ?>
    <div class=" rounded p-3">
        <p class='text-center'><?= Txt($row["level"]) ?></p>
        <?php if ($row["staff_public"] == "no") { ?>
            <font color='#ff0000'>[<?= Txt("HIDDEN") ?>]</font>
        <?php } ?>
        <div class='text-center'>
            <?= $V_table[$row["group_id"]] ?>&nbsp;
        </div>
    </div><br>
<?php } ?>
<?php block_end(); ?>