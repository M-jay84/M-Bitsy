<?php

use App\Libs\DB;
use App\Models\WarningsLogic;

 block_begin($TITLE) ?>
<p class="text-center">
    <?php echo sprintf(LANG["X_USERS_WARNED"], $COUNT); ?>
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
                <?php foreach ($WARNINGS as $row) { ?>
                    <tr>
                        <td><?= coloredname($row["id"]) ?></td>
                        <td><?= get_user_class_name($row["class"]) ?></td>
                        <td><?= user_timezone_date($row["added"]) ?></td>
                        <td><?= user_timezone_date($row["last_access"]) ?></td>
                        <?php $warnings_query = "SELECT count(id) FROM `warnings` WHERE userid = ? AND active=?"; ?>
                        <?php $count_user_warn = DB::run($warnings_query, [$row['id'],'yes'])->fetchColumn(); ?>
                        <td><a href="<?= Url("warning/$row[id]") ?>"><?= number_format($count_user_warn); ?></a></td>
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
<?= $PAGER ?>
<?php block_end() ?>