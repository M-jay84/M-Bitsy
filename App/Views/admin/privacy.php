<?php block_begin($page_title) ?>
<div class="text-center tt_blockcenter rounded p-2">
    <a href="<?= Url('adminprivacy/normal') ?>"><?= LANG["NORMAL"] ?></a> | 
    <a href="<?= Url('adminprivacy/strong') ?>"><?= LANG["STRONG"] ?></a> |  
    <a href="<?= Url('adminprivacy') ?>"><?= LANG["ALL"] ?></a>
</div><br>

<div class='table-responsive  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG["NAME"] ?></th>
                <th><?= LANG["CLASS"] ?></th>
                <th><?= LANG["PRIVACY"] ?></th>
                <th><?= LANG["EMAIL"] ?></th>
                <th><?= LANG["IP"] ?></th>
                <th><?= LANG["ADDED"] ?></th>
                <th><?= LANG["LAST_ACCESS"] ?></th>
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