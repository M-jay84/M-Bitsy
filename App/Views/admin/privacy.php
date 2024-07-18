<?php block_begin($V_title) ?>
<div class="text-center tt_blockcenter rounded p-2">
    <a href="<?= Url('adminprivacy/normal') ?>"><?= Txt("NORMAL") ?></a> | 
    <a href="<?= Url('adminprivacy/strong') ?>"><?= Txt("STRONG") ?></a> |  
    <a href="<?= Url('adminprivacy') ?>"><?= Txt("ALL") ?></a>
</div><br>

<div class='table-responsive  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt("NAME") ?></th>
                <th><?= Txt("CLASS") ?></th>
                <th><?= Txt("PRIVACY") ?></th>
                <th><?= Txt("EMAIL") ?></th>
                <th><?= Txt("IP") ?></th>
                <th><?= Txt("ADDED") ?></th>
                <th><?= Txt("LAST_ACCESS") ?></th>
            </tr>
        </thead>
        <tbody> <?php
        foreach ($V_users as $row) { ?>
            <tr>
                <td><?= coloredname($row["id"]) ?></td>
                <td><?= get_user_class_name($row["class"]) ?></td>
                <td><?= $row["privacy"] ?></td>
                <td><?= $row["email"] ?></td>
                <td><?= $row["ip"] ?></td>
                <td><?= utc_to_tz($row["added"]) ?></td>
                <td><?= utc_to_tz($row["last_access"]);?></td>
            </tr> <?php
        } ?>
        <tbody>
    </table>
</div>
<?= $V_pager ?>
<?php block_end() ?>