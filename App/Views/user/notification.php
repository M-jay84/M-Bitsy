<?php block_begin(V_notifications$title) ?>
    <div class='table-responsive  rounded p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= Txt("TITLE") ?></th>
                    <th><?= Txt("MESSAGE") ?></th>
                    <th><?= Txt("ADDED") ?></th>
                    <th><?= Txt("READ") ?></th>
                    <th><?= Txt("ACTION") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($V_notificationsnotifications as $row) : ?>
                    <tr>
                        <td><?= $row['title'] ?></td>
                        <td><a href='<?= Url("notification/read/$row[id]") ?>'><?= trim($row['message']) ?></a></td>
                        <td><?= $row['added'] ?></td>
                        <td><?= $row['read'] ?></td>
                        <td>
                            <a href='<?= Url("notification/submit/$row[id]") ?>'><?= Txt("READ") ?></a> -
                            <a href='<?= Url("notification/delete/$row[id]") ?>'><?= Txt("DEL") ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php block_end() ?>