<?php block_begin($page_title) ?>
<?php usermenu($user_id, 'warning');

if ($current_user->isStaff) : ?>
    <p class="text-center">
        <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= LANG["ADD"] ?></a>
    <p>
<?php endif; ?>

<?php if ($current_user->isStaff) : ?>
    <form id="deleteall" method="post" action="<?= Url("warning/delete/$user_id") ?>">
<?php endif; ?>

<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG["ADDED"] ?></th>
                <th><?= LANG["EXPIRE"] ?></th>
                <th><?= LANG["REASON"] ?></th>
                <th><?= LANG["WARNED_BY"] ?></th>
                <th><?= LANG["TYPE"] ?></th>
                <?php if ($current_user->isStaff) { ?>
                    <th><?= LANG["ACTION"] ?>&nbsp;<input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($warnings as $row) {
            $row['added'] = user_timezone_date($row['added']);
            $row['expiry'] = user_timezone_date($row['expiry']);
            $addeddate = substr($row['added'], 0, strpos($row['added'], " "));
            $expirydate = substr($row['expiry'], 0, strpos($row['expiry'], " "));  ?>
            <tr>
                <td><?= $addeddate ?></td>
                <td><?= $expirydate ?></td>
                <td><?=  format_comment($row['reason']) ?></td>
                <td><?= coloredname($row['warnedby']) ?></td>
                <td><?=  $row['type'] ?></td>
                <?php if ($current_user->isStaff) { ?>
                    <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" /></td>
                <?php }  ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($current_user->class->isStaff) { ?>
    <p class="text-center"><button type="submit" class="btn btn-sm ttbtn"><?= LANG['DEL'] ?></button><p>
    </form>
<?php } ?>

<?php if ($current_user->delete_users == 'yes' && $current_user->control_panel == "yes") { ?>
    <div class="tt_blockcenter rounded p-3">
        <form method='post' action='<?= Url('profile/delete') ?>'>
            <input type='hidden' name='userid' value='<?= $user_id ?>' />
            <div class='text-center'>
                <p class='text-center'><b><?= LANG["DELETE_USER"] ?></b></p>
                <div class="form-floating mb-4">
                    <input class="form-control fullwidth" type='text' size='30' name='delreason' />
                    <label fpr='delreason'><?= LANG["REASON"] ?>:</label>
                </div>
                <button type='submit' class='btn btn-sm ttbtn'><b><?= LANG["DELETE_ACCOUNT"] ?></b></button>
            </div>
        </form>
    </div>
<?php } ?>

<!-- Add Modal -->
<?php if ($current_user->edit_users == 'yes' && $current_user->control_panel == "yes") { ?>
    <form id="submit" method="post" action="<?= Url("warning/submit/$user_id") ?>">
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class='text-center'>
                            <textarea class="form-control" cols='40' rows='5' name='reason' placeholder="<?= LANG["REASON"] ?>"></textarea>
                        </div>
                        <div class="input-group mb-3">
                            <input class="form-control" type='number' size='4' placeholder="Days" name='expiry' />
                            <span></span>
                            <input class="form-control" type='text' placeholder="Type" name='type' />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG['CLOSE'] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php } ?>

<?php block_end(); ?>