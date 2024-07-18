<?php block_begin($V_title) ?>
<p class='text-center'>
    <?php echo sprintf(Txt('INVITED_USER'), number_format($V_count)); ?>
</p>
<div class=''>
    <form id="invited" method="post" action="<?= Url('admininvite/sumbit') ?>">
        <div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th><?= Txt("NAME") ?></th>
                        <th><?= Txt("EMAIL") ?></th>
                        <th><?= Txt("CLASS") ?></th>
                        <th><?= Txt("INVITED") ?></th>
                        <th><?= Txt("LAST_ACCESS") ?></th>
                        <th><?= Txt("INVITED_BY") ?></th>
                        <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($V_users as $row) : ?>
                        <tr>
                            <td><?= coloredname($row["id"]); ?></td>
                            <td><?= $row["email"]; ?></td>
                            <td><?= get_user_class_name($row["class"]); ?></td>
                            <td><?= utc_to_tz($row["added"]); ?></td>
                            <td><?= utc_to_tz($row["last_access"]); ?></td>
                            <td><?php echo ($row['inviter']) ? coloredname($row["inviter"]) : 'Unknown User'; ?></td>
                            <td><input type="checkbox" name="users[]" value="<?= $row["id"]; ?>" /></td>
                        </tr>
                    <?php endforeach; ?>
                <tbody>
            </table>
        </div>
        <div class='text-center'>
            <button type='submit' class='btn btn-sm ttbtn'><?= Txt("DEL") ?></button>
        </div>
    </form>
</div>
<?= $V_pager ?>
<?php block_end() ?>