<?php block_begin($page_title) ?>
<p class='text-center'>
    To solve this problem you must to keep seeding these torrents for <?= CONFIG['HNR_SEEDTIME'] ?> hours or until ratio becomes 1:1<br>
    But if you want a fast way, you can trade to delete these recordings with Bonus or Upload
</p>

<div class='table-responsive p-3'>
    <table class='table table-striped'>
        <thead>
            <tr>
                <th><?= LANG["TORRENT_NAME"] ?></th>
                <th><i class='fa fa-upload tticon' title='Uploaded'></i></th>
                <th><i class='fa fa-download tticon' title='Downloaded'></i></th>
                <th><?= LANG["SEED_TIME"] ?></th>
                <th><?= LANG["SIZE"] ?></th>
                <th>Remove Bonus</th>
                <th>Remove Upload</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($V_snatched as $row) {
                $smallname = htmlspecialchars(CutName($row['name'], 40)); ?>
                <tr>
                    <td><a href="<?php echo Url("torrent/$row[tid]") ?>"><b><?= $smallname ?></b></a></td>
                    <td><font color="#27B500"><?php echo mksize($row['uploaded']); ?></font></td>
                    <td><font color="#FF2200"><?php echo mksize($row['download']); ?></font></td>
                    <td class="table_col2"><?php echo ($row['uploaded']) ? get_elapsed_time($row['last_time']) : '---'; ?></td>
                    <td class="table_col2"><?php echo mksize($row['size']); ?></td>
                    <form method="post" action="<?= Url('snatch/trade') ?>">
                        <input type='hidden' name='torid' value="<?= $row['tid']; ?>">
                        <td> <?php
                            if ($row['seedbonus'] >= $row['size']) { ?>
                                <input type="submit" class="btn btn-sm ttbtn" name="requestpoints" value="Delete">&nbsp;Cost <b><?= $row['size'] ?></b> Bonus<br> <?php
                            } else { ?>
                                You don't have enough Seed Bonus for trading !<br><?php
                            } ?>
                        </td>
                        <td> <?php
                            if ($row['uploaded'] > $row['size']) { ?>
                                <input type="submit" class="btn btn-sm ttbtn" name="requestupload" value="Delete">&nbsp;Cost <b><?= mksize($row['size']); ?></b> Upload<?php
                            } else { ?>
                                You don't have enough Upload for trading !<?php
                            } ?>
                        </td>
                    </form>
                </tr><?php
            } ?>
        </tbody>
    </table>
</div>
<?php block_end() ?>