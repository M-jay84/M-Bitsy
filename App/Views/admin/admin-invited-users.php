<?php block_begin($TITLE) ?>
<p class='text-center'>
    <?php echo sprintf(LANG['INVITED_USER'], number_format($COUNT)); ?>
</p>
<div class=''>
    <form id="invited" method="post" action="<?= Url('admininvite/sumbit') ?>">
        <div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th><?= LANG["NAME"] ?></th>
                        <th><?= LANG["EMAIL"] ?></th>
                        <th><?= LANG["CLASS"] ?></th>
                        <th><?= LANG["INVITED"] ?></th>
                        <th><?= LANG["LAST_ACCESS"] ?></th>
                        <th><?= LANG["INVITED_BY"] ?></th>
                        <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($INVITES as $row) : ?>
                        <tr>
                            <td><?= coloredname($row["id"]); ?></td>
                            <td><?= $row["email"]; ?></td>
                            <td><?= get_user_class_name($row["class"]); ?></td>
                            <td><?= user_timezone_date($row["added"]); ?></td>
                            <td><?= user_timezone_date($row["last_access"]); ?></td>
                            <td><?php echo ($row['inviter']) ? coloredname($row["inviter"]) : 'Unknown User'; ?></td>
                            <td><input type="checkbox" name="users[]" value="<?= $row["id"]; ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                <tbody>
            </table>
        </div>
        <div class='text-center'>
            <button type='submit' class='btn btn-sm ttbtn'><?= LANG["DEL"] ?></button>
        </div>
    </form>
</div>
<?= $PAGER ?>
<?php block_end() ?>