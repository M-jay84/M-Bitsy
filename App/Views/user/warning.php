<?php block_begin($V_title) ?>
<?php usermenu($USER, $V_id, 'warning');

if ($USER->isStaff()) : ?>
    <p class="text-center">
        <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><?= Txt("ADD") ?></a>
    <p>
<?php endif; ?>

<?php if ($USER->isStaff()) : ?>
    <form id="deleteall" method="post" action="<?= Url("warning/delete/$V_id") ?>">
<?php endif; ?>

<div class='table-responsive'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt("ADDED") ?></th>
                <th><?= Txt("EXPIRE") ?></th>
                <th><?= Txt("REASON") ?></th>
                <th><?= Txt("WARNED_BY") ?></th>
                <th><?= Txt("TYPE") ?></th>
                <?php if ($USER->isStaff()) { ?>
                    <th><?= Txt("ACTION") ?>&nbsp;<input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($V_warnings as $row) {
            $row['added'] = utc_to_tz($row['added']);
            $row['expiry'] = utc_to_tz($row['expiry']);
            $addeddate = substr($row['added'], 0, strpos($row['added'], " "));
            $expirydate = substr($row['expiry'], 0, strpos($row['expiry'], " "));  ?>
            <tr>
                <td><?= $addeddate ?></td>
                <td><?= $expirydate ?></td>
                <td><?=  format_comment($row['reason']) ?></td>
                <td><?= coloredname($row['warnedby']) ?></td>
                <td><?=  $row['type'] ?></td>
                <?php if ($USER->isStaff()) { ?>
                    <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" /></td>
                <?php }  ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($USER->isStaff()) { ?>
    <p class="text-center"><button type="submit" class="btn btn-sm ttbtn"><?= Txt('DEL') ?></button><p>
    </form>
<?php } ?>

<?php if ($USER->permission('delete_users') && $USER->has('control_panel') == "yes") { ?>
    <div class="tt_blockcenter rounded p-3">
        <form method='post' action='<?= Url('profile/delete') ?>'>
            <input type='hidden' name='userid' value='<?= $V_id ?>' />
            <div class='text-center'>
                <p class='text-center'><b><?= Txt("DELETE_USER") ?></b></p>
                <div class="form-floating mb-4">
                    <input class="form-control fullwidth" type='text' size='30' name='delreason' />
                    <label fpr='delreason'><?= Txt("REASON") ?>:</label>
                </div>
                <button type='submit' class='btn btn-sm ttbtn'><b><?= Txt("DELETE_ACCOUNT") ?></b></button>
            </div>
        </form>
    </div>
<?php } ?>

<!-- Add Modal -->
<?php if ($USER->permission('edit_users') && $USER->has('control_panel') == "yes") { ?>
    <form id="submit" method="post" action="<?= Url("warning/submit/$V_id") ?>">
        <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class='text-center'>
                            <textarea class="form-control" cols='40' rows='5' name='reason' placeholder="<?= Txt("REASON") ?>"></textarea>
                        </div>
                        <div class="input-group mb-3">
                            <input class="form-control" type='number' size='4' placeholder="Days" name='expiry' />
                            <span></span>
                            <input class="form-control" type='text' placeholder="Type" name='type' />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= Txt("SUBMIT") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php } ?>

<?php block_end(); ?>