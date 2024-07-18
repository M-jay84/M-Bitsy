<?php block_begin($V_title) ?>
<div class='table-responsive p-2'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt("USERNAME") ?></th>
                <th><?= Txt("USERCLASS") ?></th>
                <th><?= Txt("EMAIL") ?></th>
                <th><?= Txt("IP") ?></th>
                <th><?= Txt("ADDED") ?></th>
                <th><?= Txt("COUNT") ?></th>
            </tr>
        </thead>
        <tbody><?php
        foreach ($V_users as $row) { ?>
            <tr>
                <td><?= coloredname($row["id"]); ?></td>
                <td><?= get_user_class_name($row["class"]); ?></td>
                <td><?= $row["email"] ?></td>
                <td><?= $row["ip"] ?></td>
                <td><?= utc_to_tz($row["added"]) ?></td>
                <td><a href="<?= Url("adminusersearch?ip=$row[ip]") ?>"><?= number_format($row['count']); ?></a></td>
            </tr> <?php
        } ?>
        </tbody>
    </table>
</div>
<?php block_end() ?>