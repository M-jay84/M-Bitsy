<?php block_begin($page_title) ?>
<form method='post' action='<?= Url('adminlog') ?>'>
    <p class="tt_blockcenter p-2 rounded text-center">
        <?= LANG["SEARCH"] ?>: <input type='text' size='30' name='search' />
        <input type='submit' class='btn btn-sm ttbtn' value='Search' />
    <p>
</form>

<form id='sitelog' action='<?= Url('adminlog/delete') ?>' method='post'>
    <div class='table-responsive  p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["EVENT"] ?></th>
                    <th><?= LANG["DATE"] ?></th>
                    <th><?= LANG["TIME"] ?></th>
                    <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id)" /></th>
                </tr>
            </thead>
            <tbody> <?php
                foreach ($V_logs as $row) {
                    $row['added'] = user_timezone_date($row['added']);
                    $date = substr($row['added'], 0, strpos($row['added'], " "));
                    $time = substr($row['added'], strpos($row['added'], " ") + 1); ?>
                    <tr>
                        <td><?= stripslashes($row["txt"]) ?></td>
                        <td><?= $date; ?></td>
                        <td><?= $time; ?></td>
                        <td><input type='checkbox' class='ttbtn' name='ids[]' value='<?= $row['id'] ?>' /></td>
                    </tr> <?php
                } ?>
            </tbody>
        </table>
    </div><br>

    <div class="text-center">
        <input type='submit' class='btn btn-sm ttbtn' value='<?= LANG['SUBMIT'] ?>' />
    </div>
</form>
<?= $V_pager ?>
<?php block_end() ?>