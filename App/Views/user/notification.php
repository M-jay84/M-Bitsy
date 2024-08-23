<?php block_begin('page_title') ?>
    <div class='table-responsive  rounded p-3'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th><?= LANG["TITLE"] ?></th>
                    <th><?= LANG["MESSAGE"] ?></th>
                    <th><?= LANG["ADDED"] ?></th>
                    <th><?= LANG["READ"] ?></th>
                    <th><?= LANG["ACTION"] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Vnotifications as $row) : ?>
                    <tr>
                        <td><?= $row['title'] ?></td>
                        <td><a href='<?= Url("notification/read/$row[id]") ?>'><?= trim($row['message']) ?></a></td>
                        <td><?= $row['added'] ?></td>
                        <td><?= $row['read'] ?></td>
                        <td>
                            <a href='<?= Url("notification/submit/$row[id]") ?>'><?= LANG["READ"] ?></a> -
                            <a href='<?= Url("notification/delete/$row[id]") ?>'><?= LANG["DEL"] ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php block_end() ?>