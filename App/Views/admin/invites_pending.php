<?php block_begin($page_title) ?>
<p class='text-center'>
    <?php echo sprintf(LANG['INVITED_PENDING'], number_format($V_count)); ?>
</p>

<div class=''>
    <form method="post" action="<?= Url('Admininvite/submit') ?>">
        <input type="hidden" name="pending" value="1" />
        <table class='table table-stripe'>
            <thead>
                <tr>
                    <th><?= LANG["NAME"] ?></th>
                    <th><?= LANG["EMAIL"] ?></th>
                    <th><?= LANG["INVITED"] ?></th>
                    <th><?= LANG["INVITED_BY"] ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($V_users as $row) : ?>
                    <tr>
                        <td><?= coloredname($row["id"]) ?></td>
                        <td><?= $row["email"]; ?></td>
                        <td><?= utc_to_tz($row["added"]) ?></td>
                        <td><?= coloredname($row["invited_by"]) ?></td>
                        <td><input type="checkbox" name="users[]" value="<?= $row["id"] ?>" /></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center">
            <input type="submit" class='btn btn-sm ttbtn' value="<?= LANG["DEL"] ?>" />
        </div>
    </form>
</div>
<?= $V_pager ?>
<?php block_end() ?>