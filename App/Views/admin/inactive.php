<?php block_begin($V_title) ?>
<div class="tt_blockcenter rounded p-2 text-center">
    <form action='<?= Url('admininactive') ?>' method='post'>
        <?= Txt("DAYS") ?>
        <input type='text' name='cday' size='10' value='<?= ($V_cday > 30 ? $V_cday : 30) ?>' maxlength='3' />
        <input type='hidden' name='action' value='cday' />
        <input type='submit' class='btn btn-sm ttbtn' value='<?= Txt("SUBMIT") ?>' />
    </form>
</div>

<h5 class='text-center'><?= $V_count ?> <?= Txt("INACTIVE_FOR") ?> <?= $V_cday ?> <?= Txt("DAYS") ?></h5>
<form id="inactive" action='<?= Url('admininactive/submit') ?>' method='post'>
    <div class='table-responsive  p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt("USERNAME") ?></th>
                    <th><?= Txt("CLASS") ?></th>
                    <th><?= Txt("IP") ?></th>
                    <th><?= Txt("RATIO") ?></th>
                    <th><?= Txt("JOINED") ?></th>
                    <th><?= Txt("LAST_VISIT") ?></th>
                    <th></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
                </tr>
            </thead>
            <tbody> <?php
                foreach ($V_users as $row) {
                    $userratio = $row["downloaded"] > 0 ? number_format($row["uploaded"] / $row["downloaded"], 1) : "---";
                    $last_seen = (($row["last_access"] == null) ? "never" : "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($row["last_access"])) . "&nbsp;ago");
                    ?>
                    <tr>
                        <td><?= coloredname($row["id"]) ?></td>
                        <td><?= get_user_class_name($row["class"]) ?></td>
                        <td><?php echo ($row["ip"] == "" ? "----" : $row["ip"]) ?></td>
                        <td>
                            <b><?= get_ratio_color($userratio) ?></b>
                            <b>DL: <?= mksize($row["downloaded"]) ?></b> |
                            <b>Up: <?= mksize($row["uploaded"]) ?></b>
                        </td>
                        <td><?php echo substr($row['added'], 0, strpos($row['added'], " ")) ?></td>
                        <td><?= $last_seen ?></td>
                        <td>
                            <select name='action' class='form-select'>
                                <option value=mail><?= Txt("PM") ?></option>
                                <option value='deluser'><?= Txt("DELETEUSERS") ?></option>
                                <option value='disable'><?= Txt("DISABLED_ACCOUNTS") ?></option>
                            </select>
                        </td>
                        <td><input type="checkbox" name="users[]" value="<?= $row["id"]; ?>" /></td>
                    </tr> <?php
                } ?>
            </tbody>
        </table>
        <div class="text-center">
            <input type='submit' class='btn btn-sm ttbtn' name='submit' value='<?= Txt("SUBMIT") ?>' />
        </div>
    </div>
</form>
<?php block_end() ?>