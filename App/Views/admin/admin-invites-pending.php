<?php block_begin($TITLE) ?>
<p class='text-center'>
    <?php echo sprintf(LANG['INVITED_PENDING'], number_format($COUNT)); ?>
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
                foreach ($INVITES as $row) : ?>
                    <tr>
                        <td><?= coloredname($row["id"]) ?></td>
                        <td><?= $row["email"]; ?></td>
                        <td><?= user_timezone_date($row["added"]) ?></td>
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
<?= $PAGER ?>
<?php block_end() ?>