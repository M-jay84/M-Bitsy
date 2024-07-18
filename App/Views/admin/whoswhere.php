<?php block_begin($V_title) ?>
<div class='table-responsive  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt('NAME') ?></th>
                <th><?= Txt('PAGE') ?></th>
                <th><?= Txt('DATE') ?></th>
            </tr>
        </thead>
        <tbody> <?php
        foreach ($V_users as $row) { ?>
            <tr>
                <td><b><?= coloredname($row["id"]); ?></b></td>
                <td><?= htmlspecialchars($row["page"]); ?></td>
                <td><?= utc_to_tz($row["last_access"]); ?></td>
            </tr> <?php
        } ?>
        </tbody>
    </table>
</div>
<?php block_end() ?>