<?php block_begin($V_title) ?>
<p class="text-center">
    <?php echo sprintf(Txt('MANY_PEERS'), $V_count); ?>
</p>
<?= $pager ?>
<div class='table  p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= Txt('USER') ?></th>
                <th><?= Txt('NAME') ?></th>
                <th><?= Txt('IP') ?></th>
                <th><?= Txt('PORT') ?></th>
                <th><?= Txt('UPLOADED') ?></th>
                <th><?= Txt('DOWNLOADED') ?></th>
                <th><?= Txt('PEER_ID') ?></th>
                <th><?= Txt('CONNECTED') ?></th>
                <th><?= Txt('SEEDING') ?></th>
                <th><?= Txt('STARTED') ?></th>
                <th><?= Txt('LAST_ACTION') ?></th>
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