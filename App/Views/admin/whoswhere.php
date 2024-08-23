<?php block_begin($page_title) ?>
<div class='table-responsive  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG['NAME'] ?></th>
                <th><?= LANG['PAGE'] ?></th>
                <th><?= LANG['DATE'] ?></th>
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