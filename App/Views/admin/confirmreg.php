<?php block_begin($V_title) ?>
<p class='text-center'>
    <?php echo sprintf(Txt('PENDING_REGS'), number_format($V_count)); ?>
</p>
<form id="confirmreg" class='p-1' method="post" action="<?= Url('adminconfirmreg/submit') ?>">
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt('USERNAME') ?></th>
                    <th><?= Txt('EMAIL') ?></th>
                    <th><?= Txt('ADDED') ?></th>
                    <th><?= Txt('IP') ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody> 
            <?php foreach ($V_users as $row) : ?>
                <tr>
                    <td><?= coloredname($row["id"]); ?></td>
                    <td><?= $row["email"]; ?></td>
                    <td><?= utc_to_tz($row["added"]); ?></td>
                    <td><?= $row["ip"]; ?></td>
                    <td><input type="checkbox" name="ids[]" value="<?= $row["id"]; ?>" /></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <input type="submit" class='btn btn-sm ttbtn' value="<?= Txt('CONFIRM') ?>" />
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>