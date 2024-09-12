<?php block_begin($page_title) ?>
<p class='text-center'>
    <?php echo sprintf(LANG['PENDING_REGS'], number_format($V_count)); ?>
</p>
<form id="confirmreg" class='p-1' method="post" action="<?= Url('adminconfirmreg/submit') ?>">
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG['USERNAME'] ?></th>
                    <th><?= LANG['EMAIL'] ?></th>
                    <th><?= LANG['ADDED'] ?></th>
                    <th><?= LANG['IP'] ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody> 
            <?php foreach ($V_users as $row) : ?>
                <tr>
                    <td><?= coloredname($row["id"]); ?></td>
                    <td><?= $row["email"]; ?></td>
                    <td><?= user_timezone_date($row["added"]); ?></td>
                    <td><?= $row["ip"]; ?></td>
                    <td><input type="checkbox" name="ids[]" value="<?= $row["id"]; ?>" /></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <input type="submit" class='btn btn-sm ttbtn' value="<?= LANG['CONFIRM'] ?>" />
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>