<?php block_begin($V_title) ?>
<div class='text-center'>
    <a href="<?= Url('adminban/email') ?>"><?= Txt("EMAIL") ?></a>&nbsp;
    <a href="<?= Url('adminban/ip') ?>"><?= Txt("IP") ?></a>
</div>
<p class="text-end">
    <a type="button" class="model_form ttbtn btn-sm" data-bs-toggle="modal" data-bs-target="#banModal"><?= Txt("ADD") ?></a>
<p>
<form id='delete' action='<?= Url('adminban/delete') ?>' method='post'>
    <p class="text-center"><?= Txt("BANNED_TYPE ") ?>  <?= $V_type ?></p>
    <div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt("DATE_ADDED") ?></th>
                    <th><?= Txt("FIRST") ?></th>
                    <th><?= Txt("LAST") ?></th>
                    <th><?= Txt("ADDED_BY") ?></th>
                    <th><?= Txt("COMMENT") ?></th>
                    <th><?= Txt("TYPE") ?></th>
                    <th><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_bans as $row) : ?>
                    <tr>
                        <td><?= date('d/m/Y H:i:s', utc_to_tz_time($row["added"])) ?></td>
                        <td><?= date('d/m/Y H:i:s', utc_to_tz_time($row["last"])) ?></td>
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
    <p class="text-center"><button type="submit" class="btn btn-sm ttbtn"><?= Txt("Delete") ?></button></p>
</form>

<form id="submit" method="post" action="<?= Url('adminban'); ?>">
    <div class="modal fade" id="banModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-body p-2">
                    <div class="text-center">
                        <b><?= Txt("ADD_BAN") ?></b><br>
                        <input id="first" type="text" class="form-control fullwidth" name="first" placeholder="<?= Txt("BAN_EMAIL_IP") ?>"><br>
                        <input id="last" type="text" class="form-control fullwidth" name="last" placeholder="<?= Txt("Other Ip") ?>"><br>
                        <select name="type" class='form-select'>
                            <option value="email"><?= Txt("EMAIL") ?></option>
                            <option value="ip"><?= Txt("IP") ?></option>
                            <option value="domain"><?= Txt("DOMAIN") ?></option>
                        </select><br>
                        <input id="comment" type="text" class="form-control fullwidth" name="comment" placeholder="<?= Txt("ADDCOMMENT") ?>"><br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm ttbtn" data-bs-dismiss="modal"><?= Txt("CLOSE") ?></button>
                        <button type="submit" class="btn btn-sm ttbtn"><?= Txt("BAN") ?></button>
                    </div>
                </div>
            </div>
        </div>
</form>

<?= $V_pager ?>
<?php block_end() ?>