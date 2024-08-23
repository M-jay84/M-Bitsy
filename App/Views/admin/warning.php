<?php

use App\Models\WarningsModel;

 block_begin($page_title) ?>
<p class="text-center">
    <?php echo sprintf(LANG["X_USERS_WARNED"], $V_count); ?>
</p>
<form id="deleteall" method="post" action="<?= Url('adminwarning/submit') ?>">
    <div class='table-responsive '>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["NAME"] ?></th>
                    <th><?= LANG["CLASS"] ?></th>
                    <th><?= LANG["ADDED"] ?></th>
                    <th><?= LANG["LAST_ACCESS"] ?></th>
                    <th><?= LANG["WARNINGS"] ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_warnings as $row) { ?>
                    <tr>
                        <td><?= coloredname($row["id"]) ?></td>
                        <td><?= get_user_class_name($row["class"]) ?></td>
                        <td><?= utc_to_tz($row["added"]) ?></td>
                        <td><?= utc_to_tz($row["last_access"]) ?></td>
                        <?php $count = WarningsModel::fetchColumn('count(id)', ['userid=', 'active='], [$row['id'],'yes']); ?>
                        <td><a href="<?= Url("warning/$row[id]") ?>"><?= number_format($count); ?></a></td>
                        <td><input type="checkbox" name="ids[]" value="<?= $row["id"] ?>" /></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-sm ttbtn"><?= LANG["DEL"] ?>
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>