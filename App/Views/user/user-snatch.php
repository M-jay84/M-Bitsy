<?php
block_begin($page_title); ?>

<div class='table-responsive p-3'>
    <table class='table table-striped'>
    <thead>
    <tr>
    <th><?= LANG["NAME"] ?></th>
    <th><?= LANG["UPLOADED"] ?></th>
    <th><?= LANG["DOWNLOADED"] ?></th>
    <th><?= LANG["RATIO"] ?></th>
    <th><?= LANG["ADDED"] ?></th>
    <th><?= LANG["LAST_ACTION"] ?></th>
    <th><?= LANG["SEED_TIMED"] ?></th>
    <th><?= LANG["COMPLETED"] ?></th>
    <th><?= LANG["SEEDING"] ?></th>
    <th><?= LANG["HNR"] ?></th>
    </tr>
    </thead>
    <tbody>
     <?php foreach ($V_snatched as $row) {
        $userratio = $row['download'] > 0 ? number_format($row['upload'] / $row['download'], 1) : "---";
        $complete = $row['completed'] != 'yes' ? "<font color=#27B500><b>" . LANG["YES"] . "</b></font>" : "<font color=#FF1200><b>" . LANG["NO"] . "</b></font>";
        $seeding = $row['seeding'] != 'yes' ? "<font color=#27B500><b>" . LANG["YES"] . "</b></font>" : "<font color=#FF1200><b>" . LANG["NO"] . "</b></font>";
        $hnr = $row['hnr'] != 'yes' ? "<font color=#27B500><b>" . LANG["YES"] . "</b></font>" : "<font color=#FF1200><b>" . LANG["NO"] . "</b></font>";
        $seedtime = $row['last_time'] ? get_elapsed_time($row['last_time']) : '---';
        $name = $col == 'torrent' ?  coloredname($row['id']) : htmlspecialchars(CutName($row['name'], 35));
        ?>
        <tr>
        <td><b><?= $name ?></b></td>
        <td><font color='#27B500'><?php echo mksize($row['upload']) ?></font></td>
        <td><font color='#FF1200'><?php echo  mksize($row['download']) ?></font></td>
        <td><?php echo get_ratio_color($userratio) ?></td>
        <td><?php echo user_timezone_date(date("Y-m-d H:i:s", $row['start_time'])) ?></td>
        <td><?php echo user_timezone_date(date("Y-m-d H:i:s", $row['upload_time'])) ?></td>
        <td><?= $seedtime ?></td>
        <td><?= $complete ?></td>
        <td><?= $seeding ?></td>
        <td><?= $hnr ?></td>
        </tr> <?php
    } ?>
    </tbody>
    </table>
    </div>
<?php
block_end();