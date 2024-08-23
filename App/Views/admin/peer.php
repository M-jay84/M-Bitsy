<?php block_begin($page_title) ?>
<p class="text-center">
    <?php echo sprintf(LANG['MANY_PEERS'], $V_count); ?>
</p>
<?= $pager ?>
<div class='table  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG['USER'] ?></th>
                <th><?= LANG['NAME'] ?></th>
                <th><?= LANG['IP'] ?></th>
                <th><?= LANG['PORT'] ?></th>
                <th><?= LANG['UPLOADED'] ?></th>
                <th><?= LANG['DOWNLOADED'] ?></th>
                <th><?= LANG['PEER_ID'] ?></th>
                <th><?= LANG['CONNECTED'] ?></th>
                <th><?= LANG['SEEDING'] ?></th>
                <th><?= LANG['STARTED'] ?></th>
                <th><?= LANG['LAST_ACTION'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($V_peers as $row) : ?>
            <tr>
                <?php
                $username = $row['userid'] != 0 ? coloredname($row['userid']) : $row["ip"];
                $smallname = CutName(htmlspecialchars($row["name"]), 40);
                $seeder = ($row['seeder'] == 'yes') ? 'green': 'red';
                $connectable = ($row['connectable'] == 'yes') ? 'green': 'red';
                ?>
                <td><?= $username ?></td>
                <td><a href="torrent/<?= $row['torrent'] ?>"><?= $smallname ?></a></td>
                <td><?= $row['ip'] ?></td>
                <td><?= $row['port'] ?></td>
                <?php if ($row['uploaded'] < $row['downloaded']) : ?>
                    <td><font color="red"><?= mksize($row['uploaded']) ?></font></td>
                <?php elseif ($row['uploaded'] == '0') : ?>
                    <td><?= mksize($row['uploaded']) ?></td>
                <?php else : ?>
                    <td><font color="green"><?= mksize($row['uploaded']) ?></font></td>
                <?php endif; ?>
                <td><?= mksize($row['downloaded']) ?></td>
                <td><?= substr($row["peer_id"], 0, 8) ?></td>
                <td><font color="<?= $seeder ?>"><?= $connectable ?></font></td>
                <td><font color="' . $seeder ?>"><?= $row['seeder'] ?></font></td>
                <td><?= utc_to_tz($row['started']) ?></td>
                <td><?= utc_to_tz($row['last_action']) ?></td>
            <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>
<?= $V_pager ?>
<?php block_end() ?>