<?php block_begin($page_title) ?>
<div class='text-center'>
    <a href="<?= Url('adminban/email') ?>"><?= LANG['EMAIL'] ?></a>&nbsp;
    <a href="<?= Url('adminban/ip') ?>"><?= LANG["IP"] ?></a>
</div>
<p class="text-end">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#banModal"><?= LANG["ADD"] ?></a>
<p>
<form id='delete' action='<?= Url('adminban/delete') ?>' method='post'>
    <p class="text-center"><?= LANG["BANNED_TYPE"] ?>  <?= $V_type ?></p>
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["DATE_ADDED"] ?></th>
                    <th><?= LANG['FIRST'] ?></th>
                    <th><?= LANG["ADDED_BY"] ?></th>
                    <th><?= LANG["COMMENT"] ?></th>
                    <th><?= LANG["TYPE"] ?></th>
                    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_bans as $row) : ?>
                    <tr>
                        <td><?= user_timezone_date($row["added"]) ?></td>
                        <td><?= $row['first'] ?></td>
                        <td><?= coloredname($row['addedby']) ?></td>
                        <td><?= $row['comment'] ?></td>
                        <td><?= $row['type'] ?></td>
                        <td><input type='checkbox' name='ids[]' value='<?= $row['id'] ?>' /></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="text-center"><button type="submit" class="btn btn-sm ttbtn"><?= LANG['DEL'] ?></button></p>
</form>

<form id="submit" method="post" action="<?= Url('adminban'); ?>">
    <div class="modal fade" id="banModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body p-2">
                    <div class="text-center">
                        <b><?= LANG["ADD_BAN"] ?></b><br>
                        <input id="first" type="text" class="form-control fullwidth" name="first" placeholder="<?= LANG["BAN_EMAIL_IP"] ?>"><br>
                        <select name="type" class='form-select'>
                            <option value="email"><?= LANG["EMAIL"] ?></option>
                            <option value="ip"><?= LANG["IP"] ?></option>
                            <option value="domain"><?= LANG["DOMAIN"] ?></option>
                        </select><br>
                        <input id="comment" type="text" class="form-control fullwidth" name="comment" placeholder="<?= LANG["ADDCOMMENT"] ?>"><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= LANG["CLOSE"] ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= LANG['SUBMIT'] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $V_pager ?>
<?php block_end() ?>